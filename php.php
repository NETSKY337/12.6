<?php
$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
];
function getFullnameFromParts($surname, $name, $patronymic, $examplePersonsArray = []) {
    $fullname = array_values(array_filter($examplePersonsArray, function ($person) use ($name, $surname, $patronymic) {
        [$key] = each($person);
        return strpos($person['fullname'], " {$surname}") !== false
            && strpos($person['fullname'], " {$name} ") !== false
            && strpos($person['fullname'], " {$patronymic}") !== false;
    }))[0]['fullname'] ?? null;
    if ($fullname === null) {
        $fullname = trim("{$surname} {$name} {$patronymic}");
    }

    return $fullname;
}
foreach ($example_persons_array as $person) {
    $fullname = getFullnameFromParts(...explode(' ', $person['fullname']));
    echo $fullname . PHP_EOL;
    echo "<br>";
}
function getPartsFromFullname($fullname) {
    $fullname = trim($fullname);
    preg_match('/(\p{Lu}\p{Ll}*)\s+(\p{Lu}\p{Ll}*(\s+ибн|\s+ibn|\s+bin|\s+ben|\s+son|\s+van|\s+de|\s+dela|\s+di|\s+von)?)\s+(\p{Lu}\p{Ll}*)/u', $fullname, $matches);
    $parts = [
        'surname' => $matches[2],
        'name' => $matches[1],
    ];

    if (isset($matches[4])) {
        $parts['patronomyc'] = trim($matches[4] . ' ' . $matches[5]);
    } else {
        $parts['patronomyc'] = '';
    }

    return $parts;
}
foreach ($example_persons_array as $person) {
    $fullname = getFullnameFromParts(...explode(' ', $person['fullname']));
    echo $fullname . PHP_EOL;

    $parts = getPartsFromFullname($fullname);
    print_r($parts);
    echo PHP_EOL;
    echo "<br>";
}
function getShortName($fullname) {
    $parts = getPartsFromFullname($fullname);
    return "{$parts['name']} " . mb_substr($parts['surname'], 0, 1) . ". ";
}
foreach ($example_persons_array as $person) {
    $fullname = getFullnameFromParts(...explode(' ', $person['fullname']));
    echo $fullname . PHP_EOL;

    $parts = getPartsFromFullname($fullname);
    print_r($parts);
    echo PHP_EOL;

    $shortname = getShortName($fullname);
    echo "Сокращенно: {$shortname}" . PHP_EOL;
    echo PHP_EOL;
    echo "<br>";
}
function getGenderFromName($fullname) {
    $parts = getPartsFromFullname($fullname);
    $genderScore = 0;

    if (preg_match('/ва$/i', $parts['surname'])) {
        $genderScore--;
    } elseif (preg_match('/в$/i', $parts['surname'])) {
        $genderScore++;
    }

    if (preg_match('/a$/i', $parts['name'])) {
        $genderScore--;
    } elseif (preg_match('/[йn]$/i', $parts['name'])) {
        $genderScore++;
    }

    if (preg_match('/(ич|вич)$/i', $parts['patronomyc'])) {
        $genderScore++;
    } elseif (preg_match('/на$/i', $parts['patronomyc'])) {
        $genderScore--;
    }

    if ($genderScore > 0) {
        return 1;
    } elseif ($genderScore < 0) {
        return -1;
    } else {
        return 0;
    }
}
foreach ($example_persons_array as $person) {
    $fullname = getFullnameFromParts(...explode(' ', $person['fullname']));
    echo $fullname . PHP_EOL;

    $gender = getGenderFromName($fullname);
    echo "Gender: ";
    if ($gender == 1) {
        echo "Мужской пол";
    } elseif ($gender == -1) {
        echo "Женский пол";
    } else {
        echo "Неопределенный пол";
    }
    echo PHP_EOL;
    echo "<br>";
}
function getGenderDescription($persons_array) {
    $gender_count = [
        'Мужской пол' => 0,
        'Женский пол' => 0,
        'Неопределенный пол' => 0,
    ];

    foreach ($persons_array as $person) {
        $fullname = getFullnameFromParts(...explode(' ', $person['fullname']));
        $gender = getGenderFromName($fullname);

        switch ($gender) {
            case 1:
                $gender_count['Мужской пол']++;
                break;
            case -1:
                $gender_count['Женский пол']++;
                break;
            default:
                $gender_count['Неопределенный пол']++;
                break;
        }
    }

    $total = array_sum($gender_count);
    $gender_percent = array_map(function ($count) use ($total) {
        return round(($count / $total) * 100, 1);
    }, $gender_count);

    $output = "Гендерный состав аудитории:\n---------------------------\n";
    $output .= "Мужчины - {$gender_percent['Мужской пол']}%\n";
    $output .= "Женщины - {$gender_percent['Женский пол']}%\n";
    $output .= "Не удалось определить - {$gender_percent['Неопределенный пол']}%\n";

    return $output;
}
$gender_description = getGenderDescription($example_persons_array);
echo $gender_description; 
echo "<br>";
function getPerfectPartner($sname, $name, $patronymic, $persons_array) {
    $fullname = getFullnameFromParts(ucfirst(strtolower($sname)), ucfirst(strtolower($name)), ucfirst(strtolower($patronymic)));
    $gender = getGenderFromName($fullname);

    do {
        $random_person = $persons_array[array_rand($persons_array)];
        $random_fullname = getFullnameFromParts(...explode(' ', $random_person['fullname']));
        $random_gender = getGenderFromName($random_fullname);
    } while ($random_gender == $gender);

    $compatibility = rand(5000, 10000) / 100;
    $compatibility_percent = number_format($compatibility, 2) . '%';
    $output = "{$fullname} + " . getShortName($random_fullname) . " = \n";
    $output .= "♡ идеально на {$compatibility_percent} ♡\n";

    return $output;
}

$sname = 'Иванов';
$name = 'Иван';
$patronymic = 'Иванович';

$perfect_partner = getPerfectPartner($sname, $name, $patronymic, $example_persons_array);
echo $perfect_partner;
echo "<br>";

$sname = 'Степанова';
$name = 'Наталья';
$patronymic = 'Степановна';

$perfect_partner = getPerfectPartner($sname, $name, $patronymic, $example_persons_array);
echo $perfect_partner;
echo "<br>";
// Единственное, что я не придумал, что делать с неопределенным полом.