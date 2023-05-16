<!DOCTYPE html>

<html lang="ru">

<head>

    <meta charset="UTF-8">

    <link crossorigin="anonymous" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <meta content="width = device-width, initial-scale = 1, maximum-scale = 1" name="viewport">

    <title>web4</title>

    <link rel="stylesheet" href="style.css">

    <style>
        .error {
            border: 2px red solid;
        }

        .err-msg {
            color: red;
            font-size: 16px;
            display: inline;
        }
    </style>

</head>

<body>
<?php
if (!empty($messages['success'])){
    print '<p class="text-success">' . $messages['success'] . '</p>';
}
?>
<a href="./login.php">Форма авторизации</a>
<div class="col-12 mx-auto">

    <h2 id="form_">Форма</h2>

    <form action="" method="POST" class="d-block" id="form">

        <label class="form-label">
            Имя:
            <input name="field-name" class="form-control <?php if ($errors['name']) print 'error'; ?>"
                   value="<?php print $values['name']; ?>" placeholder="Введите своё имя" required>
        </label> <?php if ($errors['name']) {
            print '<div class="err-msg">' . $messages['name'] . '</div>';
        } ?><br>

        <label class="form-label">
            Email:
            <input name="field-email" class="form-control" value="<?php print($values['email']); ?>" type="email"
                   placeholder="Введите вашу почту" required>
        </label><br>

        <label class="form-label">
            дата рождения:
            <input class="form" name="field-date" min='1999-01-01'
                   max='2007-04-01'
                   value="<?php print($values['birthday']); ?>"
                   type="date" required/>
        </label><br/>

        Пол:
        <div class="form-check-inline"><label class="form-label"><input type="radio" class="form-check-input"
                                                                        name="radio-group-1"
                                                                        value="Man" checked>Мужской</label></div>
        <div class="form-check-inline"><label class="form-label"><input type="radio"
                                                                        class="form-check-input"
                    <?php if ($values['sex'] == 'Female') {
                        print 'checked';
                    } ?>
                                                                        name="radio-group-1"
                                                                        value="Female">Женский</label><br>
        </div>
        <br>

        Количество конечностей:
        <div class="form-check-inline"><label class="form-check-label"><input type="radio"
                                                                              class="form-check-input"
                    <?php if ($values['limbs'] == '0') {
                        print 'checked';
                    } ?>
                                                                              name="radio-group-2" value="0">0</label>
        </div>
        <div class="form-check-inline"><label class="form-check-label"><input type="radio" class="form-check-input"
                    <?php if ($values['limbs'] == '1') {
                        print 'checked';
                    } ?>
                                                                              name="radio-group-2" value="1">1</label>
        </div>
        <div class="form-check-inline"><label class="form-check-label"><input type="radio" class="form-check-input"
                    <?php if ($values['limbs'] == '2') {
                        print 'checked';
                    } ?>
                                                                              name="radio-group-2" value="2">2</label>
        </div>
        <div class="form-check-inline"><label class="form-check-label"><input type="radio" class="form-check-input"
                    <?php if ($values['limbs'] == '3') {
                        print 'checked';
                    } ?>
                                                                              name="radio-group-2" value="3">3</label>
        </div>
        <div class="form-check-inline"><label class="form-check-label"><input type="radio" class="form-check-input"
                                                                              checked="checked" name="radio-group-2"
                                                                              value="4">4</label></div>
        <div class="form-check-inline"><label class="form-check-label"><input type="radio" class="form-check-input"
                    <?php if ($values['limbs'] == '5') {
                        print 'checked';
                    } ?>
                                                                              name="radio-group-2"
                                                                              value="5">5</label><br></div>
        <br>

        <label class="form-label">
            Сверхспособности:
            <select name="field-power[]" class="form-control" multiple="multiple" required>
                <?php
                foreach ($abilities as $ability){
                    $selected = empty($values['powers'][$ability['power']]) ? '' : 'selected';
                    printf('<option value="%s" %s>%s</option>', $ability['power'], $selected, $ability['power']);
                }
                ?>
            </select>
        </label><br>
        
        <label class="form-label">
            Сверхспособности:
            <select name="field-power[]" class="form-control" multiple="multiple" required>
                <option value="Immortality" <?php if (!empty($values['powers']['Immortality'])) print 'selected'; ?>>
                    Бессмертие
                </option>
                <option value="Levitation" <?php if (!empty($values['powers']['Levitation'])) print 'selected'; ?>>
                    Левитация
                </option>
                <option value="Telepathy" selected="selected">Телепатия</option>
                <option value="Telekinesis" <?php if (!empty($values['powers']['Telekinesis'])) print 'selected'; ?>>
                    Телекинез
                </option>
            </select>
        </label><br>

        <label class="form-label">
            Биография:
            <textarea name="field-biography" class="form-control <?php if ($errors['bio']) print 'error'; ?>"
                      placeholder="<?php print $values['bio']; ?>" required></textarea>
        </label>
        <?php if ($errors['bio']) {
            print '<div class="err-msg">' . $messages['bio'] . '</div>';
        } ?><br>

        <label class="form-label">С контрактом ознакомлен (-а)<input type="checkbox" class="form-check-input"
                                                                     name="cntrt" required></label><br>

        <input type="submit" class="btn btn-primary" value="Отправить">

    </form>
</div>

</body>

</html>
