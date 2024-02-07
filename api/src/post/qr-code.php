<?php 

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;


/* 

Enlarge
Margin
Shrink
*/

/**
 * QRcode
 * @param array $parameter 
 * parameter in array
 * * url : string
 * * label : string
 */
function QRcode($parameter) {
    

    $data = $parameter['data'];
    $label = $parameter['label']?$parameter['label']:'';
    $logo = ASSET_URL('/img/vendor/line/btn_base.png');

    $result = Builder::create()
        ->writer(new PngWriter())
        ->writerOptions([])
        ->data($data)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::High)
        ->size(512)
        ->margin(15)
        ->roundBlockSizeMode(RoundBlockSizeMode::Enlarge)
        ->logoPath($logo)
        ->logoResizeToWidth(100)
        ->logoPunchoutBackground(true)
        ->labelText($label)
        ->labelFont(new NotoSans(20))
        ->labelAlignment(LabelAlignment::Center)
        ->validateResult(false)
        ->build();

    // Directly output the QR code
    header('Content-Type: '.$result->getMimeType());

    echo $result->getString();

    // Save it to a file
    //$result->saveToFile(__DIR__.'/qrcode.png');

    // Generate a data URI to include image data inline (i.e. inside an <img> tag)
    $dataUri = $result->getDataUri();

    return $dataUri;
}

function qr_code_login(){
    if(!isset($_POST['url'])) {
        return ;
    }

    $url = $_POST['url'];
    $label = $_POST['label'];

    $QRcode = QRcode([
        'data' => $url,
    ]);

    return $QRcode;
}

?>