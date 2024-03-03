<?php

if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Thêm giáo viên'
];

layout('header', 'admin', $data);
layout('sidebar', 'admin', $data);
layout('breadcrumb', 'admin', $data);

// Truy vấn lấy ra danh sách nhóm
$allSubject = getRaw("SELECT id, name FROM subject ORDER BY id");

// Xử lý thêm người dùng
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


   // Validate chọn nhóm
//    if(empty(trim($body['subject_id']))) {
//     $errors['subject_id']['required'] = '** Vui lòng chọn môn học !';
//    }

   
   // Kiểm tra mảng error
  if(empty($errors)) {
    // không có lỗi nào
    $dataInsert = [
        'fullname' => $body['fullname'],
        'birthday' => $body['birthday'],
        'sex' => $body['sex'],
        'address' => $body['address'],
        'phone' => $body['phone'],
        'subject_id' => $body['subject_id'],
    ];

    $insertStatus = insert('teacher', $dataInsert);
    if ($insertStatus) {
        setFlashData('msg', 'Thêm giáo viên '.'<strong>'.$body['fullname'].'</strong>'.' thành công');
        setFlashData('msg_type', 'success');
        redirect('?module=teacher&action=lists');
    }else {
    setFlashData('msg', 'Hệ thống đang gặp sự cố, vui lòng thử lại sau');
    setFlashData('msg_type', 'danger');
    redirect('?module=teacher&action=add'); 
    }

  }else {
    // Có lỗi xảy ra
    setFlashData('msg', 'Vui lòng kiểm tra chính xác thông tin nhập vào');
    setFlashData('msg_type', 'danger');
    setFlashData('errors', $errors);
    setFlashData('old', $body);  // giữ lại các trường dữ liệu hợp lê khi nhập vào
    redirect('?module=teacher&action=add'); 
  }

}
$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
   

?>
    <div class="container">
        <hr/>
        <?php
            getMsg($msg, $msgType);
        ?>

        <form action="" method="post">
            <div class="row">
                <div class="col-5">

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
                <div class="col-5">
                    <div class="form-group">
                        <label for="">Ngày sinh</label>
                        <input type="date" name="birthday" id="" class="form-control" value="<?php echo old('birthday', $old); ?>">
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
                    <button type="submit" class="btn btn-primary">Thêm mới</button>
                    <a style="margin-left: 20px " href="<?php echo getLinkAdmin('teacher', 'lists') ?>" class="btn btn-success"><i class="fa fa-forward"></i></a>
                </div>
            </div>
        </form>
    </div>


<?php
layout('footer', 'admin');





