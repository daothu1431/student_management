<?php
$data = [
    'pageTitle' => 'Cập nhật lớp học'
];

layout('header', 'admin', $data);
layout('sidebar', 'admin', $data);
layout('breadcrumb', 'admin', $data);


$allTeacher = getRaw("SELECT * FROM teacher");

// Lấy dữ liệu cũ của người dùng
$body = getBody();

if(!empty($body['id'])) {

    $classId = $body['id'];

    $classDetail  = firstRaw("SELECT * FROM class WHERE id=$classId");

    
    if (!empty($classDetail)) {
        setFlashData('classDetail', $classDetail);
    }else {
        redirect('?module=class&action=lists');
    }
}

// Xử lý sửa người dùng
if(isPost()) {
    // Validate form
    $body = getBody(); // lấy tất cả dữ liệu trong form
    $errors = [];  // mảng lưu trữ các lỗi
    
    // Valide họ tên: Bắt buộc phải nhập, >=5 ký tự
    if(empty(trim($body['name']))) {
        $errors['name']['required'] = '** Bạn chưa nhập tên lớp!';
    }

    if(empty($body['teacher_id'])) {
        $errors['teacher_id']['required'] = '** Vui lòng chọn giáo viên chủ nhiệm!';
    }

   
   // Kiểm tra mảng error
  if(empty($errors)) {
    // không có lỗi nào
    $dataUpdate = [
        'name' => $body['name'],
        'teacher_id' => $body['teacher_id'], 
    ];

    /////////////////////////////////////
    $condition = "id=$classId";

    $updateStatus = update('class', $dataUpdate, $condition);
    if ($updateStatus) {
        setFlashData('msg', 'Cập nhật lớp học thành công');
        setFlashData('msg_type', 'success');
        redirect('?module=class&action=lists');
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

  redirect('?module=class&action=edit&id='.$classId);

}
$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
$classDetail = getFlashData('classDetail');

if (!empty($classDetail) && empty($old)) {
    $old = $classDetail;
}

?>

   <!-- Main content -->
   <section class="content">
     <div class="container-fluid">
           <form action="" method="post">
           <hr/>
               <?php echo getMsg($msg, $msgType);  ?>
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
                                    <option value="<?php echo $item['id'] ?>" <?php  echo (old('teacher_id', $old) == $item['id'])?'selected':false; ?>><?php echo $item['fullname'] ?></option> 
                                
                                <?php
                                    }
                                }
                                ?>
                        </select>
                        <?php echo form_error('teacher_id', $errors, '<span class="error">', '</span>'); ?>
                    </div>
                   </div>
             
      
          
               <div class="btn-row">
                   <button type="submit" class="btn btn-primary">Cập nhật</button>
                   <a style="margin-left: 20px " href="<?php  echo getLinkAdmin('class', 'lists') ?>" class="btn btn-success"><i class="fa fa-forward"></i></a>
                   <input type="hidden" name="id" value="<?php echo $classId; ?>">
               </div>
         
           </form>
     </div><!-- /.container-fluid -->
   </section>
   <!-- /.content -->

<?php
layout('footer', 'admin');