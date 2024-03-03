<?php

$body = getBody();

if(!empty($body['id'])) {
    $summaryId = $body['id'];
    $name = firstRaw("SELECT nameStudent FROM summary WHERE id = $summaryId");
    echo $name;
    // Kiểm tra Id có tồn tại trong hệ thống hay không
    $summaryDetail = getRows("SELECT id FROM summary WHERE id=$summaryId");
    if($summaryDetail > 0) {
        // Thực hiện xóa
       
            $deletesummary = delete('summary', "id=$summaryId");
            if($deletesummary) {
                setFlashData('msg', 'Xóa thông tin học bạ của học sinh '.'<strong>'.$name['nameStudent'].'</strong>'. ' thành công');
                setFlashData('msg_type', 'success');
            }else {
                setFlashData('msg', 'Lỗi hệ thống! Vui lòng thử lại sau');
                setFlashData('msg_type', 'danger');
            }
    }
}

redirect('?module=summary&action=lists');