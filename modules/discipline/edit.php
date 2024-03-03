<?php

if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Cập nhật thông tin kỷ luật'
];

layout('header', 'admin', $data);
layout('sidebar', 'admin', $data);
layout('breadcrumb', 'admin', $data);



// Truy vấn lấy ra danh sách class
$allClass = getRaw("SELECT id, name as className FROM class ORDER BY id");

// Truy vấn lấy ra danh sách học kỳ
$allSemester = getRaw("SELECT id, name FROM semester ORDER BY name");

// Truy vấn lấy ra danh sách năm học
$allSchoolYear = getRaw("SELECT id, year FROM schoolyear");

$alldiscipline = getRaw("SELECT discipline.class_id FROM discipline INNER JOIN class ON discipline.class_id = class.id");

// Xử lý hiện dữ liệu cũ của người dùng


$body = getBody();

if(!empty($body['id'])) {
    $disciplineId = $body['id'];   
    $disciplineDetail  = firstRaw("SELECT * FROM discipline WHERE id=$disciplineId");
    if (!empty($disciplineDetail)) {
        // Tồn tại
        // Gán giá trị disciplineDetail vào setFalsh
        setFlashData('disciplineDetail', $disciplineDetail);
    
    }else {
        redirect('?module=discipline&action=lists');
    }
}


// Xử lý sửa người dùng
if(isPost()) {
    // Validate form
    $body = getBody(); // lấy tất cả dữ liệu trong form
    $errors = [];  // mảng lưu trữ các lỗi
    
    // Valide họ tên: Bắt buộc phải nhập, >=5 ký tự
    if(empty(trim($body['nameStudent']))) {
        $errors['nameStudent']['required'] = '** Bạn chưa nhập họ tên!';
    }else {
        if(strlen(trim($body['nameStudent'])) <= 5) {
        $errors['nameStudent']['min'] = '** Họ tên phải lớn hơn 5 ký tự!';
        }
    }

  
   // Kiểm tra mảng error
  if(empty($errors)) {
    // không có lỗi nào
    $dataUpdate = [
        'nameStudent' => $body['nameStudent'],
        'class_id' => $body['class_id'],
        'schoolYear_id' => $body['schoolYear_id'],
        'day' => date('Y-m-d'),
        'reason' => $body['reason'],       
        'rank' => $body['rank'],    
    ];

    $condition = "id=$disciplineId";
    $updateStatus = update('discipline', $dataUpdate, $condition);
    if ($updateStatus) {
        setFlashData('msg', 'Cập nhật thông tin kỷ luật thành công');
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

  redirect('?module=discipline&action=edit&id='.$disciplineId);

}
$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
// $disciplineDetail = getFlashData('disciplineDetail');

if (!empty($disciplineDetail) && empty($old)) {
    $old = $disciplineDetail;
}


?>
    <div class="container">
        <hr/>
        <?php
            getMsg($msg, $msgType);
        ?>

        <form action="" method="post">
                <div class="col-4">
                    <div class="form-group">
                        <label for="">Họ tên</label>
                        <input type="text" name="nameStudent" id="" class="form-control" value="<?php echo old('nameStudent', $old); ?>">
                        <?php echo form_error('nameStudent', $errors, '<span class="error">', '</span>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="">Lớp học</label>
                        <select name="class_id" id="" class="form-select">
                            <option value="">Chọn lớp học</option>
                            <?php

                                if(!empty($allClass)) {
                                    foreach($allClass as $item) {
                                ?>
                                    <option value="<?php echo $item['id'] ?>" <?php  echo (old('class_id', $old) == $item['id'])?'selected':false; ?>>Lớp: <?php echo $item['className'] ?></option> 
                                
                                <?php
                                    }
                                }
                                ?>
                        </select>
                    </div>
                    
                
                </div>
                <div class="col-4">
                
                    <div class="form-group">
                        <label for="">Năm học</label>
                        <select name="schoolYear_id" id="" class="form-select">
                            <option value="">Chọn năm học</option>
                            <?php

                                if(!empty($allSchoolYear)) {
                                    foreach($allSchoolYear as $item) {
                                ?>
                                     <option value="<?php echo $item['id'] ?>" <?php  echo (old('schoolYear_id', $old) == $item['id'])?'selected':false; ?>><?php echo $item['year'] ?></option> 
                                
                                <?php
                                    }
                                }
                                ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Lý do kỷ luật</label>
                        <textarea name="reason" class="form-control" ><?php echo old('reason', $old); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="">Mức kỷ luật</label>
                        <input type="text" name="rank" class="form-control" value="<?php echo old('rank', $old); ?>">
                    </div>
                    
                   
                </div>              
            </div>
            <div class="col">
                <div class="btn-row">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a style="margin-left: 10px " href="?module=discipline&action=lists" class="btn btn-success"><i class="fa fa-forward"></i></a>
                    <input type="hidden" name="id" value="<?php echo $disciplineId; ?>">
                </div>
        </form>
    </div>


<?php
layout('footer', 'admin');





