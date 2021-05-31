<?php

$mf=json_decode(file_get_contents("https://w3c.github.io/json-ld-api/tests/flatten-manifest.jsonld"));
$j=[
"package smithereen.jsonld;",
"",
"import org.junit.jupiter.api.DisplayName;",
"import org.junit.jupiter.api.Test;",
"",
"import java.net.URI;",
"",
"import com.google.gson.*;",
"",
"import static org.junit.jupiter.api.Assertions.*;",
"import static smithereen.jsonld.TestUtils.*;",
"",
"class FlattenTests{",
""
];

foreach($mf->sequence as $test){
	if(isset($test->option) && isset($test->option->specVersion) && $test->option->specVersion!="json-ld-1.0"){
		echo "Skipping test {$test->name} because of incompatible spec version\n";
		continue;
	}
	if(isset($test->context)){
		continue; // not testing compaction
	}
	$j[]="\t/***\n\t* {$test->purpose}\n\t*/";
	$j[]="\t@Test";
	$j[]="\t@DisplayName(\"".str_replace('"', '\"', $test->name)."\")";
	$j[]="\tvoid ".substr($test->{"@id"}, 1)."(){";
	$type=$test->{"@type"}[0];
	$inputURL="https://w3c.github.io/json-ld-api/tests/".$test->input;
	if(!file_exists("src/test/resources/".$test->input)){
		file_put_contents("src/test/resources/".$test->input, file_get_contents($inputURL));
	}
	if(isset($test->expect) && !file_exists("src/test/resources/".$test->expect)){
		file_put_contents("src/test/resources/".$test->expect, file_get_contents("https://w3c.github.io/json-ld-api/tests/".$test->expect));
	}
	if(isset($test->option) && isset($test->option->base))
		$inputURL=$test->option->base;
	if($type=="jld:PositiveEvaluationTest"){
		$j[]="\t\tJsonElement input=readResourceAsJSON(\"/{$test->input}\");";
		$j[]="\t\tJsonElement expect=readResourceAsJSON(\"/{$test->expect}\");";
		$j[]="\t\tJsonArray flattened=JLDProcessor.flatten(input, URI.create(\"$inputURL\"));";
		$j[]="\t\tassertEquals(expect, flattened);";
	}else if($type=="jld:NegativeEvaluationTest"){
		$j[]="\t\tassertThrows(JLDException.class, ()->{";
		$j[]="\t\t\tJsonElement input=readResourceAsJSON(\"/{$test->input}\");";
		$j[]="\t\t\tJLDProcessor.flatten(input, URI.create(\"$inputURL\"));";
		$j[]="\t\t}, \"{$test->expectErrorCode}\");";
	}else{
		die("Unknown test type $type\n");
	}
	$j[]="\t}";
	$j[]="";
}

$j[]="}";

file_put_contents("src/test/java/smithereen/jsonld/FlattenTests.java", implode("\n", $j));