<?php

if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Xem chi tiết'
];

layout('header', 'admin', $data);
layout('sidebar', 'admin', $data);
layout('breadcrumb', 'admin', $data);



// Truy vấn lấy ra danh sách class
$allClass = getRaw("SELECT id, name as className FROM class ORDER BY id");


// Truy vấn lấy ra danh sách năm học
$allSchoolYear = getRaw("SELECT id, year FROM schoolyear");

$allsummary = getRaw("SELECT summary.class_id FROM summary INNER JOIN class ON summary.class_id = class.id");

// Xử lý hiện dữ liệu cũ của người dùng


$body = getBody();

if(!empty($body['id'])) {
    $summaryId = $body['id'];   
    $summaryDetail  = firstRaw("SELECT * FROM summary WHERE id=$summaryId");
    if (!empty($summaryDetail)) {
        // Tồn tại
        // Gán giá trị summaryDetail vào setFalsh
        setFlashData('summaryDetail', $summaryDetail);
    
    }else {
        redirect('?module=summary&action=lists');
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
        'conduct' => $body['conduct'],
        'score_HK1' => $body['score_HK1'],
        'score_HK2' => $body['score_HK2'],
        'total_score' => $body['total_score'],
        'rank' => $body['rank'],
        'comment' => $body['comment'],
    ];

    $condition = "id=$summaryId";
    $updateStatus = update('summary', $dataUpdate, $condition);
    if ($updateStatus) {
        setFlashData('msg', 'Cập nhật học bạ của học sinh '.'<strong>'.$body['nameStudent'].'</strong>'.' thành công');
        setFlashData('msg_type', 'success');
        redirect('?module=summary');
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

  redirect('?module=summary&action=edit&id='.$summaryId);

}
$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
// $summaryDetail = getFlashData('summaryDetail');

if (!empty($summaryDetail) && empty($old)) {
    $old = $summaryDetail;
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
                        <input type="text" name="nameStudent" id="" readonly class="form-control" value="<?php echo old('nameStudent', $old); ?>">
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
                                    <option value="<?php echo $item['id'] ?>" disabled <?php  echo (old('class_id', $old) == $item['id'])?'selected':false; ?>>Lớp: <?php echo $item['className'] ?></option> 
                                
                                <?php
                                    }
                                }
                                ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Năm học</label>
                        <select name="schoolYear_id" id="" class="form-select">
                            <option value="">Chọn năm học</option>
                            <?php

                                if(!empty($allSchoolYear)) {
                                    foreach($allSchoolYear as $item) {
                                ?>
                                     <option value="<?php echo $item['id'] ?>" disabled  <?php  echo (old('schoolYear_id', $old) == $item['id'])?'selected':false; ?>><?php echo $item['year'] ?></option> 
                                
                                <?php
                                    }
                                }
                                ?>
                        </select>
                    </div>

                    <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Điểm HK1</label>
                                    <input type="number" name="score_HK1" id="" readonly class="form-control score" value="<?php echo old('score_HK1', $old); ?>">
                                    <?php echo form_error('nameStudent', $errors, '<span class="error">', '</span>'); ?>
                                </div>
                            </div>
    
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Điểm HK2</label>
                                    <input type="number" name="score_HK2" id="" readonly class="form-control score" value="<?php echo old('score_HK2', $old); ?>">
                                    <?php echo form_error('nameStudent', $errors, '<span class="error">', '</span>'); ?>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="">Điểm TKCN</label>
                                    <input type="text" name="total_score" readonly id="average" class="form-control" value="<?php echo old('total_score', $old); ?>">
                                    <?php echo form_error('total_score', $errors, '<span class="error">', '</span>'); ?>
                                </div>
                            </div>
                        </div>   
                    
                
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="">Học lực</label>
                        <input type="text" name="rank" readonly class="form-control" value="<?php echo old('rank', $old) ?>">
                    </div>

                    <div class="form-group">
                        <label for="">Hạnh kiểm</label>
                        <input type="text" name="conduct" readonly class="form-control" value="<?php echo old('conduct', $old) ?>">
                    </div>

                    <div class="form-group">
                            <label for="">Nhận xét</label>
                            <textarea name="comment" id="" readonly class="form-control" rows="5"><?php echo old('comment', $old) ?></textarea>
                    </div>
                   
                </div>              
            </div>

            <a style="margin-left: 10px " href="?module=summary&action=lists" class="btn btn-success"><i class="fa fa-forward"></i></a>
        </form>
    </div>


<?php
layout('footer', 'admin');





 