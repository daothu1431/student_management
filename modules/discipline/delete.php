<?php

$body = getBody();

if(!empty($body['id'])) {
    $disciplineId = $body['id'];
    // Kiểm tra Id có tồn tại trong hệ thống hay không
    $userDetail = getRows("SELECT id FROM discipline WHERE id=$disciplineId");
    if($userDetail > 0) {
        // Thực hiện xóa
       
            $deletediscipline = delete('discipline', "id=$disciplineId");
            if($deletediscipline) {
                setFlashData('msg', 'Xóa quyết định kỷ luật thành công');
                setFlashData('msg_type', 'success');
            }else {
                setFlashData('msg', 'Lỗi hệ thống! Vui lòng thử lại sau');
                setFlashData('msg_type', 'danger');
            }
    }
}

redirect('?module=discipline&action=lists');