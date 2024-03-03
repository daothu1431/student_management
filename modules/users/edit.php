<?php

if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Cập nhật người dùng'
];

layout('header', 'admin', $data);
layout('sidebar', 'admin', $data);
layout('breadcrumb', 'admin', $data);



// Truy vấn lấy ra danh sách nhóm
$allGroups = getRaw("SELECT id, name FROM groups ORDER BY id");

// Xử lý hiện dữ liệu cũ của người dùng
$body = getBody();
if(!empty($body['id'])) {
    $userId = $body['id'];
   
    $userDetail  = firstRaw("SELECT * FROM users WHERE id=$userId");
    if (!empty($userDetail)) {
        // Tồn tại
        // Gán giá trị userDetail vào setFalsh
        setFlashData('userDetail', $userDetail);
    
    }else {
    redirect('?module=users&action=lists');
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

   // Validate Email : Bắt buộc phải nhập, định dạng email, k trùng nhau

   if(empty(trim($body['email']))) {
     $errors['email']['required'] = '** Bạn chưa nhập Email!';
   }else {
    //Kiểm tra eamil có hợp lệ không
    if (!isEmail(trim($body['email']))) {
        $errors['email']['isEmail'] = '** Email không hợp lệ!';
    }else {
        //Kiểm tra Email có tồn tại trong DB không
        $email = trim($body['email']);
        $sql = "SELECT id FROM users WHERE email='$email' AND id<>$userId";
        if(getRows($sql) > 0) {
            $errors['email']['unique'] = '** Địa chỉ email đã tồn tại'; 
        }
    }
   }

   //Validate nhập lại Password: Bắt buộc phải nhập, giống Password
   if (!empty(trim($body['password']))) {
    // Chỉ validate comfirm_password nếu password được nhập 
       if(empty(trim($body['confirm_password']))) {
            $errors['confirm_password']['required'] = '** Bạn chưa xác nhận lại mật khẩu !';
       }else{
            if(trim($body['password']) != trim($body['confirm_password'])) {
                $errors['confirm_password']['match'] = '** Xác nhận lại mật khẩu chưa trùng khớp';
            }
       }
   }
   
   // Kiểm tra mảng error
  if(empty($errors)) {
    // không có lỗi nào
    $dataUpdate = [
        'email' => $body['email'],
        'fullname' => $body['fullname'],
        'group_id' => $body['group_id'],
        'status' => $body['status'],
        'update_at' => date('Y-m-d H:i:s'),
    ];

    // Trường hợp password được sửa
    if (!empty(trim($body['password']))) { 
        $dataUpdate['password'] = password_hash($body['password'], PASSWORD_DEFAULT);
    }
    /////////////////////////////////////
    $condition = "id=$userId";
    $updateStatus = update('users', $dataUpdate, $condition);
    if ($updateStatus) {
        setFlashData('msg', 'Cập nhật người dùng thành công');
        setFlashData('msg_type', 'success');
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

  redirect('?module=users&action=edit&id='.$userId);

}
$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
// $userDetail = getFlashData('userDetail');

if (!empty($userDetail) && empty($old)) {
    $old = $userDetail;
}

?>
    <div class="container">
        <hr/>
        <hr/>
        <?php
            getMsg($msg, $msgType);
        ?>

        <form action="" method="post">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="">Họ tên</label>
                        <input type="text" name="fullname" id="" class="form-control" value="<?php echo old('fullname', $old); ?>">
                        <?php echo form_error('fullname', $errors, '<span class="error">', '</span>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="text" name="email" id="" class="form-control"  value="<?php echo old('email', $old); ?>">
                        <?php echo form_error('email', $errors, '<span class="error">', '</span>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="">Nhóm người dùng</label>
                        <select name="group_id" id="" class="form-control">
                            <option value="">Chọn nhóm</option>
                            <?php

                                if(!empty($allGroups)) {
                                    foreach($allGroups as $item) {
                                ?>
                                    <option value="<?php echo $item['id'] ?>" <?php  echo (old('group_id', $old) == $item['id'])?'selected':false; ?>><?php echo $item['name'] ?></option> 
                                
                                <?php
                                    }
                                }
                                ?>
                        </select>
                        <?php echo form_error('group_id', $errors, '<span class="error">', '</span>'); ?>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="">Mật khẩu</label>
                        <input type="password" name="password" id="" class="form-control" placeholder="Không nhập nếu không thay đổi...">
                        <?php echo form_error('password', $errors, '<span class="error">', '</span>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="">Nhập lại mật khẩu</label>
                        <input type="password" name="confirm_password" id="" class="form-control" placeholder="Không nhập nếu không thay đổi...">
                        <?php echo form_error('confirm_password', $errors, '<span class="error">', '</span>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="">Trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="">Chọn trạng thái</option>
                            <option value="0" <?php echo (old('status', $old) == 0) ? 'selected':false; ?>>Chưa kích hoạt</option>
                            <option value="1" <?php echo (old('status', $old) == 1) ? 'selected':false; ?>>Đã kích hoạt</option>
                        </select>
                    </div>
                </div>              
            </div>
            <div class="col">
                <div class="btn-row">
                    <button type="submit" class="btn btn-primary">Cập nhật người dùng</button>
                    <a style="margin-left: 40px " href="?module=users&action=lists" class="btn btn-success"><i class="fa fa-forward"></i></a>
                    <input type="hidden" name="id" value="<?php echo $userId; ?>">
                </div>
            </div>
        </form>
    </div>


<?php
layout('footer', 'admin');





