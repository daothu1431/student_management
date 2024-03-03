<?php

$body = getBody();

if(!empty($body['id'])) {
    $conductId = $body['id'];
    // Kiểm tra Id có tồn tại trong hệ thống hay không
    $userDetail = getRows("SELECT id FROM conduct WHERE id=$conductId");
    if($userDetail > 0) {
        // Thực hiện xóa
       
            $deleteconduct = delete('conduct', "id=$conductId");
            if($deleteconduct) {
                setFlashData('msg', 'Xóa thông tin hạnh kiểm học sinh thành công');
                setFlashData('msg_type', 'success');
            }else {
                setFlashData('msg', 'Lỗi hệ thống! Vui lòng thử lại sau');
                setFlashData('msg_type', 'danger');
            }
    }
}

redirect('?module=conduct&action=lists');