<?php

if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Cập nhật thông tin giáo viên'
];

layout('header', 'admin', $data);
layout('sidebar', 'admin', $data);
layout('breadcrumb', 'admin', $data);



// Xử lý hiện dữ liệu cũ của người dùng
$body = getBody();
if(!empty($body['id'])) {
    $teacherId = $body['id'];
   
    $teacherDetail  = firstRaw("SELECT * FROM teacher WHERE id=$teacherId");
    if (!empty($teacherDetail)) {
        // Tồn tại
        // Gán giá trị teacherDetail vào setFalsh
        setFlashData('teacherDetail', $teacherDetail);
    
    }else {
        redirect('?module=teacher&action=lists');
    }
}


// Xử lý sửa người dùng
if(isPost()) {
    // Validate form
    $body = getBody(); // lấy tất cả dữ liệu trong form
    $errors = [];  // mảng lưu trữ các lỗi
    
    // Valide họ tên: Bắt buộc phải nhập, >=5 ký tự
    if(empty(trim($body['fullname']))) {
        $errors['fullname']['required'] = '** Bạn chưa nhập họ tên!';
    }else {
        if(strlen(trim($body['fullname'])) <= 5) {
        $errors['fullname']['min'] = '** Họ tên phải lớn hơn 5 ký tự!';
        }
    }

  
   // Kiểm tra mảng error
  if(empty($errors)) {
    // không có lỗi nào
    $dataUpdate = [
        'fullname' => $body['fullname'],
        'birthday' => $body['birthday'],
        'sex' => $body['sex'],
        'address' => $body['address'],
        'phone' => $body['phone'],
    ];

    $condition = "id=$teacherId";
    $updateStatus = update('teacher', $dataUpdate, $condition);
    if ($updateStatus) {
        setFlashData('msg', 'Cập nhật thông tin giáo viên '.'<strong>'.$body['fullname'].'</strong>'.' thành công');
        setFlashData('msg_type', 'success');
        redirect('?module=teacher&action=lists');
    }else {
    setFlashData('msg', 'Hệ thống đang gặp sự cố, vui lòng thử lại sau');
    setFlashData('msg_type', 'danger');
}

  }else {
    // Có lỗi xảy ra
    setFlashData('msg', 'Vui lòng kiểm tra chính xác thông tin nhập vào');
    setFlashData('msg_type', 'danger');
    setFlashData('errors', $errors);
    setFlashData('old', $body);  // giữ lại các trường dữ liệu hợp lê khi nhập vào
  }

  redirect('?module=teacher&action=edit&id='.$teacherId);

}
$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
// $teacherDetail = getFlashData('teacherDetail');

if (!empty($teacherDetail) && empty($old)) {
    $old = $teacherDetail;
}

?>
    <div class="container">
        <hr/>
        <?php
            getMsg($msg, $msgType);
        ?>

        <form action="" method="post">
        <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label for="">Họ tên</label>
                        <input type="text" name="fullname" id="" class="form-control" value="<?php echo old('fullname', $old); ?>">
                        <?php echo form_error('fullname', $errors, '<span class="error">', '</span>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="">Địa chỉ</label>
                        <input type="text" name="address" id="" class="form-control" value="<?php echo old('address', $old); ?>">
                        <?php echo form_error('address', $errors, '<span class="error">', '</span>'); ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="">Số điện thoại</label>
                        <input type="text" name="phone" id="" class="form-control" value="<?php echo old('phone', $old); ?>">
                        <?php echo form_error('phone', $errors, '<span class="error">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="">Ngày sinh</label>
                        <input type="text" name="birthday" id="" class="form-control" value="<?php echo old('birthday', $old); ?>">
                        <?php echo form_error('birthday', $errors, '<span class="error">', '</span>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="">Giới tính</label>
                        <input type="text" name="sex" id="" class="form-control" value="<?php echo old('sex', $old); ?>">
                        <?php echo form_error('sex', $errors, '<span class="error">', '</span>'); ?>
                    </div>
                   
                </div>              
            </div>
            <div class="col">
                <div class="btn-row">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a style="margin-left: 10px " href="?module=teacher&action=lists" class="btn btn-success"><i class="fa fa-forward"></i></a>
                    <input type="hidden" name="id" value="<?php echo $teacherId; ?>">
                </div>
            </div>
        </form>
    </div>


<?php
layout('footer', 'admin');





