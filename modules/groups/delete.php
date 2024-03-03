<?php

$body = getBody();

if(!empty($body['id'])) {

    $groupId = $body['id'];
    // Kiểm tra Id có tồn tại trong hệ thống hay không
    $groupDetail = getRows("SELECT id FROM groups WHERE id=$groupId");
    if($groupDetail > 0) {
        // Thực hiện xóa
        $condition = "id=$groupId";

        // Kiểm tra xem trong nhóm còn người dùng không
        $userNum = getRows("SELECT id FROM users WHERE group_id=$groupId");
        if($userNum > 0) {
            setFlashData('msg', 'Xóa nhóm người dùng không thành công. Trong nhóm còn '.$userNum.' người dùng !');
            setFlashData('msg_type', 'danger');
        }else {
            $deleteStatus = delete('groups', $condition);
            if(!empty($deleteStatus)) {
                setFlashData('msg', 'Xóa nhóm người dùng thành công');
                setFlashData('msg_type', 'success');
            }else {
                setFlashData('msg', 'Xóa nhóm người dùng không thành công. Vui lòng kiểm tra lại !');
                setFlashData('msg_type', 'danger');
            } 
        }

    }
}


redirect('?module=groups&action=lists');