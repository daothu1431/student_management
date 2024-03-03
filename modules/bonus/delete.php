<?php

$body = getBody();

if(!empty($body['id'])) {
    $bonusId = $body['id'];
    // Kiểm tra Id có tồn tại trong hệ thống hay không
    $userDetail = getRows("SELECT id FROM bonus WHERE id=$bonusId");
    if($userDetail > 0) {
        // Thực hiện xóa
       
            $deletebonus = delete('bonus', "id=$bonusId");
            if($deletebonus) {
                setFlashData('msg', 'Xóa quyết định khen thưởng thành công');
                setFlashData('msg_type', 'success');
            }else {
                setFlashData('msg', 'Lỗi hệ thống! Vui lòng thử lại sau');
                setFlashData('msg_type', 'danger');
            }
    }
}

redirect('?module=bonus&action=lists');