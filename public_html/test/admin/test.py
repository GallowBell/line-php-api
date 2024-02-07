# FILEPATH: /path/to/your/django/project/test/views.py
from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
from datetime import datetime
from .models import IotTemp, DeviceList, Hospcode

@csrf_exempt
def api(request):
    if request.method == 'GET':
        action = request.GET.get('action', None)

        if not action:
            return JsonResponse({
                'status': 400,
                'message': 'Bad Request',
            }, status=400)

        def group_by(array, key):
            result = {}
            for item in array:
                key_value = item[key] or ''
                if key_value not in result:
                    result[key_value] = []
                result[key_value].append(item)
            return result

        def get_temp_by_date(parameter):
            date_start = parameter.get('date_start', None)
            date_end = parameter.get('date_end', None)
            hospcode = parameter.get('hospcode', None)
            number = parameter.get('number', None)

            if not date_start or not date_end:
                return JsonResponse({
                    'status': 400,
                    'message': 'Bad Request',
                }, status=400)

            result = IotTemp.objects.filter(hospcode=hospcode, number=number, date_time__range=[date_start, date_end]).order_by('id')

            count = 0
            filter = request.POST.get('filter', None)
            last_hour = ''
            index = 0
            data = []
            max_temp = None
            min_temp = None

            for row in result:
                data.append({
                    'id': row.id,
                    'temp': row.temp,
                    'mac_address': row.mac_address,
                    'date_time': row.date_time,
                    'device_id': row.device_id,
                })

                if max_temp is None or data[index]['temp'] > max_temp:
                    max_temp = data[index]['temp']
                if min_temp is None or data[index]['temp'] < min_temp:
                    min_temp = data[index]['temp']

                count += 1

                if filter == 'hour':
                    date_time = datetime.strptime(row.date_time, '%Y-%m-%d %H:%M:%S')
                    hour = date_time.hour
                    if hour == last_hour:
                        del data[index]
                        continue
                    last_hour = hour

                index += 1

            return JsonResponse({
                'status': 200,
                'message': 'OK',
                'max': max_temp,
                'min': min_temp,
                'data': data,
                'total': count,
            })

        if action == 'getTempByDate':
            return get_temp_by_date(request.POST)

        if action == 'getHospcode':
            result = Hospcode.objects.filter(status=1).order_by('hospcode')
            data = [{'hospcode': row.hospcode, 'name': row.name} for row in result]
            return JsonResponse({
                'status': 200,
                'message': 'OK',
                'data': data,
            })

        if action == 'getLastTemp':
            hospcode = request.POST.get('hospcode', None)
            if not hospcode:
                return JsonResponse({
                    'status': 400,
                    'message': 'Bad Request',
                }, status=400)

            result = IotTemp.objects.raw('''
                SELECT 
                    it.*,
                    h.name
                FROM test_iottemp it
                INNER JOIN (
                    SELECT hospcode, number, MAX(id) as MaxID
                    FROM test_iottemp
                    WHERE hospcode = %s
                    GROUP BY hospcode, number
                ) grouped_it ON it.hospcode = grouped_it.hospcode AND it.number = grouped_it.number AND it.id = grouped_it.MaxID
                INNER JOIN test_devicelist DL ON DL.device_id = it.device_id AND DL.is_active = 1
                LEFT JOIN test_hospcode h ON h.namehos = it.hospcode
                ORDER BY it.id DESC;
            ''', [hospcode])

            data = [{'id': row.id, 'temp': row.temp, 'mac_address': row.mac_address, 'date_time': row.date_time, 'device_id': row.device_id, 'name': row.name} for row in result]
            return JsonResponse({
                'status': 200,
                'message': 'OK',
                'data': data,
            })

        if action == 'getLastTempAll':
            result = IotTemp.objects.raw('''
                SELECT 
                    it.*,
                    h.name
                FROM test_iottemp it
                LEFT JOIN test_hospcode h ON h.namehos = it.hospcode
                INNER JOIN test_devicelist DL ON DL.device_id = it.device_id AND DL.is_active = 1
                INNER JOIN (
                    SELECT 
                        hospcode,
                        number,
                        MAX(id) as MaxID
                    FROM test_iottemp
                    GROUP BY hospcode, number
                ) grouped_it ON it.hospcode = grouped_it.hospcode AND it.number = grouped_it.number AND it.id = grouped_it.MaxID
                ORDER BY it.id DESC;
            ''')

            data = [{'id': row.id, 'temp': row.temp, 'mac_address': row.mac_address, 'date_time': row.date_time, 'device_id': row.device_id, 'name': row.name} for row in result]
            result = group_by(data, 'hospcode')
            return JsonResponse({
                'status': 200,
                'message': 'OK',
                'data': result,
            })

        if action == 'getDeviceByHosname':
            hospcode = request.POST.get('hospcode', None)
            result = DeviceList.objects.filter(hospcode=hospcode)
            data = [{'device_id': row.device_id, 'hospcode': row.hospcode} for row in result]
            return JsonResponse({
                'status': 200,
                'message': 'OK',
                'data': data,
            })

        if action == 'getDeviceList':
            result = DeviceList.objects.raw('''
                SELECT 
                    DL.*,
                    h.namehos
                FROM test_devicelist DL
                LEFT JOIN test_hospcode h ON h.namehos = DL.hospcode
                ORDER BY DL.hospcode ASC;
            ''')

            data = [{'device_id': row.device_id, 'hospcode': row.hospcode, 'namehos': row.namehos} for row in result]
            if not data:
                return JsonResponse({
                    'status': 500,
                    'message': 'Internal Server Error',
                    'data': [],
                }, status=500)

            return JsonResponse({
                'status': 200,
                'message': 'OK',
                'data': data,
            })

        if action == 'changeStatus':
            is_active = request.POST.get('is_active', None)
            device_id = request.POST.get('device_id', None)

            if not is_active or not device_id:
                return JsonResponse({
                    'status': 400,
                    'message': 'Bad Request',
                }, status=400)

            is_active_value = 1 if is_active == 'true' else 0
            DeviceList.objects.filter(device_id=device_id).update(is_active=is_active_value)

            return JsonResponse({
                'status': 200,
                'message': 'OK',
            })

    return JsonResponse({
        'status': 400,
        'message': 'Bad Request',
    }, status=400)
