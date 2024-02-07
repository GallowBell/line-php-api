const express = require('express');
const app = express();
const bodyParser = require('body-parser');

app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

const config = require('./config');

app.get('/api', (req, res) => {
    const action = req.query.action || false;

    if (!action) {
        res.status(400).json({
            status: 400,
            message: 'Bad Request',
        });
        return;
    }

    function groupBy(array, key) {
        const result = {};
        array.forEach((item) => {
            const keyValue = item[key] || '';
            if (!result[keyValue]) {
                result[keyValue] = [];
            }
            result[keyValue].push(item);
        });
        return result;
    }

    function getTempByDate(parameter) {
        const { date_start, date_end, hospcode, number } = parameter;

        if (!date_start || !date_end) {
            res.status(400).json({
                status: 400,
                message: 'Bad Request',
            });
            return;
        }

        const sql = 'SELECT * FROM `iot_temp` WHERE `hospcode` = ? AND `number` = ? AND date_time BETWEEN ? AND ? ORDER BY `id` ASC;';
        const params = [hospcode, number, date_start, date_end];

        const result = db_Temp.select(sql, params);

        let count = 0;
        const filter = req.body.filter || false;
        let last_hour = '';
        let index = 0;
        const data = [];
        let max = false;
        let min = false;

        result.forEach((row) => {
            data[index] = {
                id: parseInt(row.id),
                temp: parseFloat(row.temp),
                mac_address: row.mac_address,
                date_time: row.date_time,
                device_id: row.device_id,
            };

            if (data[index].temp > max || !max) {
                max = data[index].temp;
            }
            if (data[index].temp < min || !min) {
                min = data[index].temp;
            }

            count++;

            if (filter === 'hour') {
                const date_time = new Date(row.date_time);
                const hour = date_time.getHours().toString();
                if (hour === last_hour) {
                    delete data[index];
                    return;
                }
                last_hour = hour;
            }

            index++;
        });

        res.json({
            status: 200,
            message: 'OK',
            max,
            min,
            data,
            total: count,
        });
    }

    if (action === 'getTempByDate') {
        getTempByDate(req.body);
        return;
    }

    if (action === 'getHospcode') {
        const sql = 'SELECT * FROM `hospcode` WHERE `status` = 1 ORDER BY `hospcode` ASC;';
        const result = db_Temp.select(sql);
        res.json({
            status: 200,
            message: 'OK',
            data: result,
        });
        return;
    }

    if (action === 'getLastTemp') {
        const hospcode = req.body.hospcode || false;
        if (!hospcode) {
            res.status(400).json({
                status: 400,
                message: 'Bad Request',
            });
            return;
        }

        const sql = `SELECT 
                        it.*,
                        h.name
                FROM iot_temp it
                INNER JOIN (
                        SELECT hospcode, number, MAX(id) as MaxID
                        FROM iot_temp
                        WHERE hospcode = ?
                        GROUP BY hospcode, number
                ) grouped_it ON it.hospcode = grouped_it.hospcode AND it.number = grouped_it.number AND it.id = grouped_it.MaxID
                INNER JOIN device_list DL ON DL.device_id = it.device_id AND DL.is_active = 1
                LEFT JOIN hospcode h ON h.namehos = it.hospcode
                ORDER BY it.id DESC;`;

        const params = [hospcode];

        const result = db_Temp.select(sql, params);
        res.json({
            status: 200,
            message: 'OK',
            data: result,
        });
        return;
    }

    if (action === 'getLastTempAll') {
        const sql = `SELECT 
                                it.*,
                                h.name 
                        FROM iot_temp it 
                        LEFT JOIN hospcode h ON h.namehos = it.hospcode 
                        INNER JOIN device_list DL ON DL.device_id = it.device_id AND DL.is_active = 1
                        INNER JOIN ( 
                                SELECT 
                                        hospcode,
                                        number,
                                        MAX(id) as MaxID 
                                FROM iot_temp 
                                GROUP BY hospcode, number 
                        ) grouped_it ON it.hospcode = grouped_it.hospcode AND it.number = grouped_it.number AND it.id = grouped_it.MaxID 
                        ORDER BY it.id DESC;`;

        const data = db_Temp.select(sql);
        const result = groupBy(data, 'hospcode');

        res.json({
            status: 200,
            message: 'OK',
            data: result,
        });
        return;
    }

    if (action === 'getDeviceByHosname') {
        const sql = 'SELECT * FROM `device_list` WHERE hospcode = ?';
        const params = [req.body.hospcode];

        const result = db_Temp.select(sql, params);
        res.json({
            status: 200,
            message: 'OK',
            data: result,
        });
        return;
    }

    if (action === 'getDeviceList') {
        const sql = 'SELECT * FROM `device_list` DL LEFT JOIN `hospcode` h ON `h`.`namehos` = `DL`.`hospcode` ORDER BY `DL`.`hospcode` ASC;';
        const result = db_Temp.select(sql);

        if (!result) {
            res.status(500).json({
                status: 500,
                message: 'Internal Server Error',
                data: [],
            });
            return;
        }

        res.json({
            status: 200,
            message: 'OK',
            data: result,
        });
        return;
    }

    if (action === 'changeStatus') {
        const is_active = req.body.is_active;
        const device_id = req.body.device_id;

        if (!is_active || !device_id) {
            res.status(400).json({
                status: 400,
                message: 'Bad Request',
            });
            return;
        }

        const sql = 'UPDATE `device_list` SET `is_active` = ? WHERE `device_id` = ?;';
        const is_active_value = is_active === 'true' ? 1 : 0;
        const params = [is_active_value, device_id];

        const result = db_Temp.update(sql, params);

        if (!result) {
            res.status(500).json({
                status: 500,
                message: 'Internal Server Error',
            });
            return;
        }

        res.json({
            status: 200,
            message: 'OK',
        });
        return;
    }

    res.status(400).json({
        status: 400,
        message: 'Bad Request',
    });
});

app.listen(3000, () => {
    console.log('Server is running on port 3000');
});