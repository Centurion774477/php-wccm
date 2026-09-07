<?php

declare(strict_types=1);

getfile:
echo "WCCM Re-implementation. Please provide a file:";
$file = readline();

if ($file == "" || $file == null ) {
    echo "Invalid file provided: your input was null.";
    goto getfile;
}

$table = [];

# very primitive verification for now
function verifyFile(array $lines): array {
    foreach ($lines as $line) {
        $index = 0;
        if ($index == 0) {
            if (!preg_match("/(.*)\s+|\s+(.*)\s+|/", $line)) {
                return [
                    false, 
                    1, 
                    <<<EOT
                    Line one is dedicated for the word and the pronunciation.
                    It should look like:
                    drink | dɹINkH |
                    EOT
                ];
            }
        } elseif ($index == 2) {
            if (!preg_match("/\w+\s+\w+/", $line)) {
                return [
                    false, 
                    2, 
                    <<<EOT
                    The second line is dedicated for the parts of speech.
                    For example:
                    noun verb
                    EOT
                ];
            }
        } elseif ($index == 3) {
            if (!preg_match("/\w+\s+\w+/", $line)) {
                return [
                    false, 
                    3, 
                    <<<EOT
                    The third line is for the plural form of the word.
                    It should look like this:
                    plural drinks
                    EOT
                ];
            }
        }
        $index += 1;
    }
    return [true, ""];
}


function lex(string $line, int $index): void {
    if ($index == 1) {
        $tuple = explode("", $line);
        array_push($table, [
        "word" => $tuple[0],
        "pronunciation" => $tuple[1],
        ]);
    } elseif ($index == 2) {
        $partsOfSpeech = explode("", $line);
        array_push($table, [
        "parts_of_speech" => $partsOfSpeech
        ]);
    } elseif ($index == 3) {
        $pluralForm = explode("plural", $line);
        array_push($table, [
            "plural_form"=> $pluralForm[1],
        ]);
    } elseif ($index == 5) {
        $meanings = [];
        while ($line != "--") {
            array_push($meanings, $line);
        }
        array_push($table, $meanings);
    } elseif ($line == "--") {
        $synonyms = [];
        while ($line != "~~") {
            array_push($synonyms, $line);
        }
        array_push($table, $synonyms);
    } elseif ($line == "~~") {
        $antonyms = [];
        while ($line != "" && $line != null) {
            array_push($antonyms, $line);
        }
        array_push($table, $antonyms);
    }
}

function generate(array $table): void {
    try {
        $handle = fopen("wccm.md", "w");
        if ($handle) {
            fwrite($handle, "{$table["word"]} | {$table["pronunciation"]} |" . PHP_EOL);
            fwrite($handle, "{$table["parts_of_speech"]}". PHP_EOL);
            fwrite($handle, "Plural: {$table["plural_form"]}". PHP_EOL);

            fwrite($handle, PHP_EOL);

            fwrite($handle, "Meanings". PHP_EOL);
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

$verification = verifyFile($lines);
if ($verification[0] == false) {
    echo "Your file does not follow the conventions.";
    echo "Broken line: {$verification[1]}";
    echo $verification[2];
    exit;
}

foreach ($lines as $line) {
    $index = 1;
    lex($line, $index);
    $index += 1;
}
