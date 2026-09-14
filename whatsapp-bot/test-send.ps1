$body = @{
    number = "628133103966"
    message = "Tes pesan dari bot PresensiKita - cek apakah pesan terbaca."
} | ConvertTo-Json

$result = Invoke-RestMethod -Uri "http://127.0.0.1:3000/send-message" -Method Post -ContentType "application/json" -Body ([System.Text.Encoding]::UTF8.GetBytes($body))
$result | ConvertTo-Json
