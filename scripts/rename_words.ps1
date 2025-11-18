$root='c:\xampp\htdocs\a_i_s_l_project\si\public\storage\signs\words'
$map=@{
    'شكرا.png'='shukran.png'
    'مرحبا.png'='marhaba.png'
    'لا.png'='laa.png'
    'موافق.png'='naam.png'
}
foreach($k in $map.Keys){
    $src=Join-Path $root $k
    $new=$map[$k]
    $dst=Join-Path $root $new
    if(Test-Path $src){
        if(Test-Path $dst){
            Write-Output "Skipping $k -> $new (target exists)"
        } else {
            try{
                Move-Item -LiteralPath $src -Destination $dst -ErrorAction Stop
                Write-Output "Renamed $k -> $new"
            } catch {
                Write-Output ("Failed to rename {0}: {1}" -f $k, $_.Exception.Message)
            }
        }
    } else {
        Write-Output "Not found: $k"
    }
}
Write-Output 'Current files:'
Get-ChildItem -Path $root | Select-Object -ExpandProperty Name
