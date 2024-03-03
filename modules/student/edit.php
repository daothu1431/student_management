<?php

if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Cập nhật thông tin học sinh'
];

layout('header', 'admin', $data);
layout('sidebar', 'admin', $data);
layout('breadcrumb', 'admin', $data);



// Truy vấn lấy ra danh sách nhóm
$allClass = getRaw("SELECT id, name FROM class ORDER BY id");

// Xử lý hiện dữ liệu cũ của người dùng
$body = getBody();


    @$classId = $body['class_id'];
   @$keyword = $body['keyword'];


if(!empty($body['id'])) {
    $studentId = $body['id'];   
    $studentDetail  = firstRaw("SELECT * FROM student WHERE id=$studentId");
    if (!empty($studentDetail)) {
        // Tồn tại
        // Gán giá trị studentDetail vào setFalsh
        setFlashData('studentDetail', $studentDetail);
    
    }else {
        redirect('?module=student&action=lists');
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
        'thumb' => $body['thumb'],
        'birthday' => $body['birthday'],
        'sex' => $body['sex'],
        'address' => $body['address'],
        'class_id' => $body['class_id'],
    ];

    $condition = "id=$studentId";
    $updateStatus = update('student', $dataUpdate, $condition);
    if ($updateStatus) {
        setFlashData('msg', 'Cập nhật thông tin học sinh '.'<strong>'.$body['fullname'].'</strong>'. ' thành công');
        setFlashData('msg_type', 'success');
        redirect('?module=student');
        // redirect('admin/?class_id='.$classId.'&keyword='.$keyword.'&module=student');
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

  redirect('?module=student&action=edit&id='.$studentId);

}
$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
// $studentDetail = getFlashData('studentDetail');

if (!empty($studentDetail) && empty($old)) {
    $old = $studentDetail;
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
                        <label for="name">Ảnh</label>
                        <div class="row ckfinder-group">
                            <div class="col-8">
                                <input type="text" name="thumb" id="name" class="form-control image-render" value="<?php echo old('thumb', $old); ?>">   
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-success choose-image"><i class="nav-icon fas fa-solid fa-upload"></i></button>
                            </div>
                        </div>
                    </div>

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
                        <label for="">Lớp học</label>
                        <select name="class_id" id="" class="form-select">
                            <option value="">Chọn lớp học</option>
                            <?php

                                if(!empty($allClass)) {
                                    foreach($allClass as $item) {
                                ?>
                                    <option value="<?php echo $item['id'] ?>" <?php  echo (old('class_id', $old) == $item['id'])?'selected':false; ?>>Lớp: <?php echo $item['name'] ?></option> 
                                
                                <?php
                                    }
                                }
                                ?>
                        </select>
                        <?php echo form_error('class_id', $errors, '<span class="error">', '</span>'); ?>
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
                    <a style="margin-left: 10px " href="?module=student&action=lists" class="btn btn-success"><i class="fa fa-forward"></i></a>
                    <input type="hidden" name="id" value="<?php echo $studentId; ?>">
                </div>
            </div>
        </form>
    </div>


<?php
layout('footer', 'admin');





