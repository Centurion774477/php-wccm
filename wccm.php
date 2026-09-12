<?php

declare(strict_types=1);

getfile:
echo "\nPHP-WCCM. Please provide a file (type 'exit' to leave):" . PHP_EOL;
$file = readline();

if ($file === "exit") {
    exit;
}

if ($file == "" || $file == null ) {
    echo "Invalid file provided: your input was null.";
    goto getfile;
}

if (!file_exists($file)) {
    echo "The given file: $file does not exist in this context.";
    goto getfile;
}

$consumingMeanings = false;
$meanings = [];

$consumingSynonyms = false;
$synonyms = [];

$consumingAntonyms = false;
$antonyms = [];

$table = [
    "word"            => "",
    "pronunciation"   => "",
    "parts_of_speech" => "",
    "plural_form"     => "",
    "meanings"        => [],
    "synonyms"        => [],
    "antonyms"        => []
];

function lex(string $line, int $index): void {
    global $consumingMeanings, $consumingSynonyms, $consumingAntonyms, $table;

    if ($index == 1) {
        $tuple = array_map('trim', explode('|', $line));
        $table["word"] = $tuple[0];
        $table["pronunciation"] = $tuple[1];
        return;
    } elseif ($index == 2) {
        $table["parts_of_speech"] = $line;
        return;
    } elseif ($index == 3) {
        $table["plural_form"] = trim($line);
    }
    // elseif ($index == 4) {
    //     $pluralForm = explode("plural", $line);
    //     # $table["plural_form"] = trim($pluralForm[1] ?? '');

    //     echo "Plural form:";
    //     echo(trim($pluralForm[1] ?? ''));

    //     $consumingMeanings = true;
    //     $consumingSynonyms = false;
    //     $consumingAntonyms = false;
    //     return;
    // }

    if ($line == "--") {
        $consumingMeanings = false;
        $consumingSynonyms = true;
        return;
    } 
    if ($line == "~~") {
        $consumingSynonyms = false;
        $consumingAntonyms = true;
        return;
    }

    if ($consumingMeanings && $line != "") {
        $table["meanings"][] = $line;
    } elseif ($consumingSynonyms && $line != "") {
        $table["synonyms"][] = $line;
    } elseif ($consumingAntonyms && $line != "") {
        $table["antonyms"][] = $line;
    }
}

function generate(): void {
    global $table;
    try {
        $handle = fopen("wccm.md", "w");
        if ($handle) {
            fwrite($handle, "{$table["word"]} | {$table["pronunciation"]} | {$table["parts_of_speech"]}" . PHP_EOL);

            $plural_form = ucfirst($table["plural_form"]);
            $plural = preg_replace("/^Plural/", "Plural:", $plural_form);
            fwrite($handle, $plural . PHP_EOL);

            fwrite($handle, PHP_EOL);

            fwrite($handle, "Meanings:". PHP_EOL);
            foreach ($table["meanings"] as $meaning) {
                fwrite($handle, "  -$meaning" . PHP_EOL);
            }

            fwrite($handle, "Synonyms:". PHP_EOL);
            foreach ($table["synonyms"] as $synonym) {
                fwrite($handle, "  -$synonym" . PHP_EOL);
            }

            fwrite($handle, "Antonyms:". PHP_EOL);
            foreach ($table["antonyms"] as $antonym) {
                fwrite($handle, "  -$antonym" . PHP_EOL);
            }
        }
    } catch (Exception $exception) {
        echo "An error occured while generating your output file: "
            . $exception->getMessage() . PHP_EOL;
    } finally {
        fclose($handle);
    }
}

$fileContent = file_get_contents($file);
$lines = explode(PHP_EOL, $fileContent);


$index = 1;
foreach ($lines as $line) {
    lex($line, $index);
    $index += 1;
}

generate();

echo "Thank you for using PHP-WCCM. Your output can be seen in wccm.md";
