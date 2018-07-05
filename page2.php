
<?php
/*
เราใช้ page2.php เป็นทั้งไฟล์ที่ทำการแสดงผลและบันทึกข้อมูลกระทู้ใหม่
ดังนั้นเราจะตรวจสอบว่าการเรียกไฟล์นี้นั้นเป็นการบันทึกหรือไม่ด้วยค่าของตัวแปร $_SERVER['REQUEST_METHOD']
ซึ่งมันจะมีค่าเป็น 'POST' หากมีการ submit มาจาก <form> ที่มี method="post"
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  /*
  ตรวจสอบให้แน่ชัดว่ามีข้อมูลที่จำเป็นส่งมาครบหรือไม่ด้วย isset()
  ซึ่งจะเป็นจริงหากใน $_POST มี key ที่ต้องการครบ
  */
  if (!isset($_POST['code'], $_POST['fname'], $_POST['lname'])) {
    /*
    หากไม่ครบก็ให้ redirect ไปที่ index.php
    */
    header('Location: index.php');
    exit;
  }
  /*
     กำหนดสีแสดงข้อความที่ไม่ได้กรอกข้อมูลมา
  */
  $color = "danger";
  /*
  เราจะ copy $_POST มาไว้ในตัวแปร $DATA
  ด้วยเหตุผลที่ว่าเราจะไม่เปลี่ยนแปลงค่าของ $_POST โดยตรง
  และเพื่อให้เป็นแนวทางเดียวกันกับทุกตัวแปรที่จะส่งไปยัง template
  */

  $DATA = $_POST;
  /*
  ทำการ trim() (ตัดช่องว่างหน้าและหลัง) ของข้อมูลใน $DATA ทุกตัว
  */
  foreach ($DATA as $key => $value) {
    $DATA[$key] = trim($value);
  }
  /*
  ตรวจสอบว่า $DATA['code'] เป็นค่าว่างหรือไม่
  จะเห็นว่าเราใช้ === เปรียบเทียบกับ empty string โดยไม่ใช้ empty() หรือ !$DATA['code']
  เพราะการเปรียบเทียบด้วยวิธีหลังเป็นการเปรียบเทียบแบบ loose คือมันจะเป็นจริงได้ในหลายกรณีเกินไป
  เช่น เมื่อ $DATA['code'] มีค่าเท่ากับ string '0' ซึ่งไม่ตรงความต้องการของเราแน่ๆ
  */
  if ($DATA['code'] === '') {
    /*
    กำหนดค่าให้กับตัวแปร $FORM_ERRORS เพื่อนำไปใช้ใน inc/form_errors.inc.php ต่อไป
    */
    $FORM_ERRORS['code'] = "กรุณาระบุ 'รหัสประจำตัว'";
  }
  /*
  และตรวจสอบความยาวของ $DATA['code'] ว่ามีความยาวมากกว่าที่กำหนดหรือไม่
  ด้วย mb_strlen() ซึ่งเราไม่ใช้ strlen()
  เพราะว่า strlen() จะตรวจจำนวน byte ไม่ใช่จำนวนตัวอักษร
  ซึ่งปัจจุบันเราใช้ encoding ชนิด UTF-8 เป็นหลัก ตัวอักษร 1 ตัวอาจจะมีความยาวมากกว่า 1 byte
  อย่างภาษาไทย ทุกตัวอักษรจะมีขนาด 3 bytes
  */
  elseif (mb_strlen($DATA['code'], 'UTF-8') > 255) {
    $FORM_ERRORS['code'] = "'รหัสประจำตัว' ต้องมีความยาวไม่เกิน 255 ตัวอักษร";
  }
  /*
  ทำการตรวจสอบกับข้อมูลอื่นๆ เช่นเดียวกัน
  */
  if ($DATA['fname'] === '') {
    $FORM_ERRORS['fname'] = "กรุณาระบุ 'ฃื่อ'";
  } elseif (mb_strlen($DATA['fname'], 'UTF-8') > 65535) {
    $FORM_ERRORS['fname'] = "'ฃื่อ' ต้องมีความยาวไม่เกิน 65535 ตัวอักษร";
  }

  if ($DATA['lname'] === '') {
    $FORM_ERRORS['lname'] = "กรุณาระบุ 'นามสกุล'";
  } elseif (mb_strlen($DATA['lname'], 'UTF-8') > 64) {
    $FORM_ERRORS['lname'] = "'นามสกุล' ต้องมีความยาวไม่เกิน 64 ตัวอักษร";
  }
  /*
  ถ้าไม่มีตัวแปร $FORM_ERRORS ถูกสร้างขึ้นมาจากการตรวจสอบข้างต้น แสดงว่าไม่มี error
  ข้อมูลทั้งหมดสามารถ INSERT เข้าฐานข้อมูลได้อย่างปลอดภัย
  */
  if (!isset($FORM_ERRORS)) {
    /*
    ทำการเชื่อมต่อกับฐานข้อมูล ดู (inc/mysqli.inc.php)
    ซึ่งเราไม่จำเป็นต้องเชื่อมต่อตั้งแต่แรกเพราะยังไม่จำเป็นต้องใช้จนกว่าจะแน่ใจว่าข้อมูลนั้นถูกต้องทั้งหมด
    */
    require 'inc/mysqli.inc.php';
    /*
    ส่ง SQL query ไปยัง MySQL Server ด้วย mysqli::query()
    โดยเราจะ escape ข้อมูลที่มาจากภายนอกทั้งหมดด้วย mysqli::escape_string()
    โดยใช้ฟังก์ชั่น sprintf() ช่วย ดู (inc/main.inc.php สำหรับ sprintf())
    */
    $mysqli->query(
      /*
      mysqli::escape_string() จะแปลงตัวอักษรพิเศษ เช่น ' ให้เป็น \' หรือ ''
      ซึ่งทำให้ MySQL Server รู้ว่ามันเป็นข้อมูล ไม่ใช่ delimeter
      หากเราไม่ใช้ mysqli::escape_string() และผ่านข้อมูลไปเป็น SQL query โดยตรง
      อาจจะทำให้เกิด error หรือ SQL Injection ขึ้นได้
      และนี่คือข้อดีของการใช้ mysqli ในแบบ OOP คือจะเห็นว่าเราสามารถแทนที่
      $mysqli->escape_string() ลงไปใน double quote string ได้เลย
      แต่ถ้าเราใช้ mysqli_escape_string() ที่เป็น procedural style
      จะไม่สามารถทำแบบนี้ได้
      */
      "
      INSERT INTO `topic`
      (
        `last_commented`,
        `code`,
        `fname`,
        `lname`,
        `ip`
      )
      VALUES
      (
        NOW(),
        '{$mysqli->escape_string($DATA['code'])}',
        '{$mysqli->escape_string($DATA['fname'])}',
        '{$mysqli->escape_string($DATA['lname'])}',
        '{$_SERVER['REMOTE_ADDR']}'
      )
      "
    );
    /*
    redirect ไปยัง index.php โดยส่ง query string ชื่อ highlight_id
    ที่มีค่าเป็น id ของแถวที่เพิ่ง INSERT เข้าไปในตาราง topic ไปด้วย
    เพื่อใช้เน้นสีพื้นหลังของกระทู้ใหม่ (ดู inc/index.inc.php)
    */
    header('Location: index.php?highlight_id=' . $mysqli->insert_id);
    /*
    จบการทำงาน
    */
    exit;
  }
  /*
  หากมี error ก็จะแสดงผลให้ผู้ใช้แก้ไขข้อมูลให้ถูกต้อง
  */
} else {
  /*
  หากไม่ใช่การ POST ก็ให้กำหนดค่า default สำหรับ $DATA ซึ่งจะถูกใช้งานใน inc/page2.php
  โดยให้เป็นค่าว่างทั้งหมด
  */
  $DATA = array(
    'code' => '',
    'fname' => '',
    'lname' => '',
  );
}
$TAGS = array('PHP', 'JavaScript', 'SQL', 'HTML', 'CSS');
/*
บอก template/template.php ให้ require ไฟล์ inc/page2.php เป็น template
*/

?>

<?php
/********** เริ่ม FORM ตั้งกระทู้ใหม่ **********/
/*
โดย form นี้จะใช้ method POST ในการส่งข้อมูลไปยัง page2.php
ข้อมูลที่จะส่งให้กับ page2.php ก็ได้แก่
code เป็น input type=text
fname เป็น textarea
และ name เป็น input type=text
*/
?>


<form action="?url=page2" method="post" class="form-horizontal panel panel-default">
  <div class="panel-heading">
    <h4>
      <span class="glyphicon glyphicon-pencil"></span>
      <?=$breadcrumb?>
    </h4>
  </div>
  <div class="panel-body">
    <?php
    /*
    แสดง errors (ถ้ามี)
    ดูคำอธิบายใน inc/form_errors.inc.php
    */
    require 'inc/message_errors.inc.php';
    ?>
    <div class="form-group <?php
    /*
    ถ้ามี key ชื่อ 'code' อยู่ใน array $FORM_ERRORS
    ให้เพิ่ม class 'has-error' เข้าไปใน <div> นี้
    */
    if (isset($FORM_ERRORS['code'])) {
      echo 'has-error';
    }
    ?>">
      <label for="codeInput" class="col-sm-4 control-label">*รหัสประจำตัว</label>
      <div class="col-sm-4">
        <input
          type="text"
          id="codeInput"
          name="code"
          value="<?php
          echo htmlspecialchars($DATA['code'], ENT_QUOTES, 'UTF-8');
          ?>"
          placeholder="รหัสประจำตัว"
          spellcheck="false"
          class="form-control"
        >
      </div>
    </div>
    <div class="form-group <?php
    /*
    ถ้ามี key ชื่อ 'fname' อยู่ใน array $FORM_ERRORS
    ให้เพิ่ม class 'has-error' เข้าไปใน <div> นี้
    */
    if (isset($FORM_ERRORS['fname'])) {
      echo 'has-error';
    }
    ?>">
      <label for="fnameInput" class="col-sm-4 control-label">*ฃื่อ</label>
      <div class="col-sm-4">
        <input
          type="text"
          id="fnameInput"
          name="fname"
          value="<?php
          echo htmlspecialchars($DATA['fname'], ENT_QUOTES, 'UTF-8');
          ?>"
          placeholder="ฃื่อ"
          spellcheck="false"
          class="form-control"
        >
      </div>
    </div>
    <div class="form-group <?php
    /*
    ถ้ามี key ชื่อ 'lname' อยู่ใน array $FORM_ERRORS
    ให้เพิ่ม class 'has-error' เข้าไปใน <div> นี้
    */
    if (isset($FORM_ERRORS['lname'])) {
      echo 'has-error';
    }
    ?>">
      <label for="nameInput" class="col-sm-4 control-label">*นามสกุล</label>
      <div class="col-sm-4">
        <input
          type="text"
          id="lnameInput"
          name="lname"
          value="<?php
          echo htmlspecialchars($DATA['lname'], ENT_QUOTES, 'UTF-8');
          ?>"
          placeholder="นามสกุล"
          spellcheck="false"
          class="form-control"
        >
      </div>
    </div>
    <hr>
    <div class="form-group">
      <div class="col-sm-4 col-sm-offset-4">
        <button type="submit" class="btn btn-primary btn-block">
          ตั้งกระทู้
        </button>
      </div>
    </div>
  </div>
</form>
<?php /********** จบ FORM แสดงความเห็น **********/ ?>
