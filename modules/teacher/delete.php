<?php

$body = getBody();

if(!empty($body['id'])) {
    $teacherId = $body['id'];
    // Kiểm tra Id có tồn tại trong hệ thống hay không
    $teacherDetail = getRows("SELECT id FROM teacher WHERE id=$teacherId");
    if($teacherDetail > 0) {
        // Thực hiện xóa
       
            $deleteteacher = delete('teacher', "id=$teacherId");
            if($deleteteacher) {
                setFlashData('msg', 'Xóa giáo viên thành công');
                setFlashData('msg_type', 'success');
            }else {
                setFlashData('msg', 'Lỗi hệ thống! Vui lòng thử lại sau');
                setFlashData('msg_type', 'danger');
            }
    }else {
        setFlashData('msg', 'Giáo viên không tồn tại trên hệ thống');
        setFlashData('msg_type', 'danger');
    }
}

redirect('?module=teacher&action=lists');