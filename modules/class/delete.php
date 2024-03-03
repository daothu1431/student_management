<?php

$body = getBody();

if(!empty($body['id'])) {

    $classId = $body['id'];
    // Kiểm tra Id có tồn tại trong hệ thống hay không
    $classDetail = getRows("SELECT id FROM class WHERE id=$classId");
    if($classDetail > 0) {
        // Thực hiện xóa
        $condition = "id=$classId";

        // Kiểm tra xem trong nhóm còn người dùng không
        $userNum = getRows("SELECT id FROM student WHERE class_id=$classId");
        if($userNum > 0) {
            setFlashData('msg', 'Xóa lớp học không thành công. Trong lớp còn '.$userNum.' học sinh !');
            setFlashData('msg_type', 'danger');
        }else {
            $deleteStatus = delete('class', $condition);
            if(!empty($deleteStatus)) {
                setFlashData('msg', 'Xóa lớp học thành công');
                setFlashData('msg_type', 'success');
            }else {
                setFlashData('msg', 'Xóa lớp học không thành công. Vui lòng kiểm tra lại !');
                setFlashData('msg_type', 'danger');
            } 
        }

    }
}


redirect('?module=class&action=lists');