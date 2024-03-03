<?php

$body = getBody();

if(!empty($body['id'])) {
    $subjectsId = $body['id'];
    // Kiểm tra Id có tồn tại trong hệ thống hay không
    $subDetail = getRows("SELECT id FROM subjects WHERE id=$subjectsId");
    if($subDetail > 0) {
        // Thực hiện xóa
       
            $deletesubjects = delete('subjects', "id=$subjectsId");
            if($deletesubjects) {
                setFlashData('msg', 'Xóa môn học thành công');
                setFlashData('msg_type', 'success');
            }else {
                setFlashData('msg', 'Lỗi hệ thống! Vui lòng thử lại sau');
                setFlashData('msg_type', 'danger');
            }
    }else {
        setFlashData('msg', 'Môn học không tồn tại trên hệ thống');
        setFlashData('msg_type', 'danger');
    }
}

redirect('?module=subjects&action=lists');