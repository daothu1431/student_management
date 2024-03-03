<?php

$body = getBody();

if(!empty($body['id'])) {
    $scoreId = $body['id'];
    // Kiểm tra Id có tồn tại trong hệ thống hay không
    $userDetail = getRows("SELECT id FROM score WHERE id=$scoreId");
    if($userDetail > 0) {
        // Thực hiện xóa
       
            $deletescore = delete('score', "id=$scoreId");
            if($deletescore) {
                setFlashData('msg', 'Xóa học sinh thành công');
                setFlashData('msg_type', 'success');
            }else {
                setFlashData('msg', 'Lỗi hệ thống! Vui lòng thử lại sau');
                setFlashData('msg_type', 'danger');
            }
    }else {
        setFlashData('msg', 'Học sinh không tồn tại trên hệ thống');
        setFlashData('msg_type', 'danger');
    }
}

redirect('?module=score&action=lists');