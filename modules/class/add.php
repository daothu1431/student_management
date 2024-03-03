<?php
$data = [
    'pageTitle' => 'Thêm lớp học'
];

layout('header', 'admin', $data);
layout('sidebar', 'admin', $data);
layout('breadcrumb', 'admin', $data);


$allTeacher = getRaw("SELECT * FROM teacher");

if(isPost()) {
     // Validate form
     $body = getBody(); // lấy tất cả dữ liệu trong form
     $errors = [];  // mảng lưu trữ các lỗi

     // Valide họ tên: Bắt buộc phải nhập, >=5 ký tự
    if(empty(trim($body['name']))) {
        $errors['name']['required'] = '** Bạn chưa nhập tên lớp!';
    }

    // Kiểm tra mảng error
  if(empty($errors)) {

    $dataInsert = [
        'name' => $body['name'],
        'teacher_id' => $body['teacher_id'], 
    ];
    $insertStatus = insert('class', $dataInsert);
    if ($insertStatus) {
        setFlashData('msg', 'Thêm lớp học thành công');
        setFlashData('msg_type', 'success');
        redirect('?module=class&action=lists');
    }else {
        setFlashData('msg', 'Hệ thống đang gặp sự cố, vui lòng thử lại sau');
        setFlashData('msg_type', 'danger');
        redirect('?module=class&action=add');
    }

  }else {  
    // Có lỗi xảy ra
    setFlashData('msg', 'Vui lòng kiểm tra chính xác thông tin nhập vào');
    setFlashData('msg_type', 'danger');
    setFlashData('errors', $errors);
    setFlashData('old', $body);  // giữ lại các trường dữ liệu hợp lê khi nhập vào
    redirect('?module=class&action=add'); // Load lại trang 
  }
}

$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
   
?>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
            <form action="" method="post">
            <hr/>
                
                    <div class="col-6">
                        <div class="form-group">
                            <label for="name">Tên lớp</label>
                            <input type="text" name="name" id="name" class="form-control" value="<?php echo old('name', $old); ?>">
                            <?php echo form_error('name', $errors, '<span class="error">', '</span>'); ?>
                        </div>

                        <div class="form-group">
                        <label for="">Giáo viên chủ nhiệm</label>
                        <select name="teacher_id" id="" class="form-select">
                            <option value="">Chọn giáo viên</option>
                            <?php

                                if(!empty($allTeacher)) {
                                    foreach($allTeacher as $item) {
                                ?>
                                    <option value="<?php echo $item['id'] ?>" <?php  echo (!empty($teacherId) && $teacherId == $item['id'])?'selected':false; ?>><?php echo $item['fullname'] ?></option> 
                                
                                <?php
                                    }
                                }
                                ?>
                        </select>
                        <?php echo form_error('teacher_id', $errors, '<span class="error">', '</span>'); ?>
                        </div>
                        
                        <div class="btn-row">
                            <button type="submit" class="btn btn-primary">Thêm mới</button>
                            <a style="margin-left: 20px " href="<?php  echo getLinkAdmin('class', 'lists') ?>" class="btn btn-success"><i class="fa fa-forward"></i></a>
                        </div>
                    </div>     
            </form>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

<?php
layout('footer', 'admin');