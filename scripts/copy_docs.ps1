Set-Location "$PSScriptRoot\..\docs"

$map = @{
    '22_SECURITY_GUIDELINE.md' = '27'
    '23_CODING_STANDARD.md'    = '28'
    '21_API_GUIDELINE.md'      = '29'
    '26_UI_GUIDELINE.md'       = '30'
    '24_DATABASE_SCHEMA.md'    = '31'
    '25_LIBRARY_MODULE.md'     = '32'
    '27_DEPLOYMENT.md'         = '33'
    '28_CPANEL_DEPLOYMENT.md'  = '34'
    '29_ROADMAP.md'            = '35'
    '30_RELEASE_PLAN.md'       = '36'
    '31_AI_GUIDELINE.md'       = '37'
    '32_PROMPT_LIBRARY.md'     = '38'
}

foreach ($src in $map.Keys) {
    $newNum = $map[$src]
    $baseName = $src -replace '^\d+_', ''
    $dest = "${newNum}_${baseName}"
    if (Test-Path $src) {
        $content = Get-Content $src -Raw -Encoding UTF8
        $oldNum = $src -replace '_.*', ''
        $pattern = "# .+ $oldNum "
        $replacement = "# 🌌 $newNum "
        $content = $content -replace $pattern, $replacement
        Set-Content -Path $dest -Value $content -Encoding UTF8 -NoNewline
        Write-Host "OK: $src -> $dest"
    }
    else {
        Write-Host "SKIP: $src not found"
    }
}