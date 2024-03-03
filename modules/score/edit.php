<?php

if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Cập nhật điểm'
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

$allScore = getRaw("SELECT score.class_id FROM score INNER JOIN class ON score.class_id = class.id");

// Xử lý hiện dữ liệu cũ của người dùng


$body = getBody();


    @$classId = $body['class_id'];
    @$keyword = $body['keyword'];
    @$semesterId = $body['semester_id'];
    @$yearId = $body['schoolYear_id'];


if(!empty($body['id'])) {
    $scoreId = $body['id'];   
    $scoreDetail  = firstRaw("SELECT * FROM score WHERE id=$scoreId");
    if (!empty($scoreDetail)) {
        // Tồn tại
        // Gán giá trị scoreDetail vào setFalsh
        setFlashData('scoreDetail', $scoreDetail);
    
    }else {
        redirect('?module=score&action=lists');
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
        'class_id' => $body['class_id'],
        'semester_id' => $body['semester_id'],
        'schoolYear_id' => $body['schoolYear_id'],
        'score_Toan' => $body['score_Toan'],
        'score_TV' => $body['score_TV'],
        'score_Eng' => $body['score_Eng'],
        'score_GDCD' => $body['score_GDCD'],
        'score_TBC' => $body['score_TBC'],
    ];

    $condition = "id=$scoreId";
    $updateStatus = update('score', $dataUpdate, $condition);
    if ($updateStatus) {
        setFlashData('msg', 'Cập nhật điểm thành công');
        setFlashData('msg_type', 'success');
        redirect('?class_id='.$classId.'&semester_id='.$semesterId.'&year_id='.$yearId.'&keyword='.$keyword.'&module=score');
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

  redirect('?module=score&action=edit&id='.$scoreId);

}
$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
// $scoreDetail = getFlashData('scoreDetail');

if (!empty($scoreDetail) && empty($old)) {
    $old = $scoreDetail;
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
                        <label for="">Lớp học</label>
                        <select name="class_id" id="" class="form-select">
                            <option value="">Chọn lớp học</option>
                            <?php

                                if(!empty($allClass)) {
                                    foreach($allClass as $item) {
                                ?>
                                    <option value="<?php echo $item['id'] ?>" <?php  echo (old('class_id', $old) == $item['id'])?'selected':false; ?>><?php echo $item['className'] ?></option> 
                                
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
                        <label for="">Học kỳ</label>
                        <select name="semester_id" id="" class="form-select">
                            <option value="">Chọn học kỳ</option>
                            <?php

                                if(!empty($allSemester)) {
                                    foreach($allSemester as $item) {
                                ?>
                                    <option value="<?php echo $item['id'] ?>" <?php  echo (old('semester_id', $old) == $item['id'])?'selected':false; ?>><?php echo $item['name'] ?></option> 
                                
                                <?php
                                    }
                                }
                                ?>
                        </select>
                        <?php echo form_error('class_id', $errors, '<span class="error">', '</span>'); ?>
                    </div>
                    
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
                        <?php echo form_error('class_id', $errors, '<span class="error">', '</span>'); ?>
                    </div>
                </div>              
            </div>

            <div class="row">
                    <div class="col-2">
                            <div class="form-group">
                                <label for="">Điểm Toán</label>
                                <input type="number" name="score_Toan" id="" class="form-control score" value="<?php echo old('score_Toan', $old); ?>">
                            </div>
                    </div>

                    <div class="col-2">
                            <div class="form-group">
                                <label for="">Điểm Tiếng Việt</label>
                                <input type="number" name="score_TV" id="" class="form-control score" value="<?php echo old('score_TV', $old); ?>">
                            </div>
                    </div>

                    <div class="col-2">
                            <div class="form-group">
                                <label for="">Điểm Tiếng Anh</label>
                                <input type="number" name="score_Eng" id="" class="form-control score" value="<?php echo old('score_Eng', $old); ?>">
                            </div>
                    </div>

                    <div class="col-2">
                            <div class="form-group">
                                <label for="">Điểm GDCD</label>
                                <input type="number" name="score_GDCD" id="" class="form-control score" value="<?php echo old('score_GDCD', $old); ?>">
                            </div>
                    </div>

                    <div class="col-2">
                            <div class="form-group">
                                <label for="">Điểm TKHK</label>
                                <input type="text" name="score_TBC" id="average" class="form-control" value="<?php echo old('score_TBC', $old); ?>">
                            </div>
                    </div>
            </div>
            <div class="col">
                <div class="btn-row">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a style="margin-left: 10px " href="?module=score&action=lists" class="btn btn-success"><i class="fa fa-forward"></i></a>
                    <input type="hidden" name="id" value="<?php echo $scoreId; ?>">
                </div>
            </div>
        </form>
    </div>


<?php
layout('footer', 'admin');





