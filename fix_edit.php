<?php

$file = 'c:\\Agathiyan\\employee-management-system\\resources\\views\\admin\\employees\\edit.blade.php';
$content = file_get_contents($file);

// Replace input fields
$content = preg_replace('/(<input[^>]*class="[^"]*form-control[^"]*"[^>]*name="([^"]+)"[^>]*>)/', '$1
                                @error(\'$2\')<div class="invalid-feedback">{{ $message }}</div>@enderror', $content);

// Replace select fields
$content = preg_replace('/(<select[^>]*class="[^"]*form-(control|select)[^"]*"[^>]*name="([^"]+)"[^>]*>)/', '$1
                                @error(\'$3\')<div class="invalid-feedback">{{ $message }}</div>@enderror', $content);

// Replace textarea fields
$content = preg_replace('/(<textarea[^>]*class="[^"]*form-control[^"]*"[^>]*name="([^"]+)"[^>]*>.*<\/textarea>)/sU', '$1
                                @error(\'$2\')<div class="invalid-feedback">{{ $message }}</div>@enderror', $content);

// Add is-invalid class dynamically
$content = preg_replace('/class="([^"]*form-(control|select)[^"]*)"([^>]*)name="([^"]+)"/', 'class="$1 @error(\'$4\') is-invalid @enderror"$3name="$4"', $content);

file_put_contents($file, $content);
echo "Done";
