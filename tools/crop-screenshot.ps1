Param(
  [string]$Source = "screenshot.png",
  [int]$TargetW = 1200,
  [int]$TargetH = 900
)

Add-Type -AssemblyName System.Drawing

if (-not (Test-Path $Source)) { throw "No se encontró $Source" }

$img = [System.Drawing.Image]::FromFile($Source)
$tmp = [System.IO.Path]::ChangeExtension($Source, '.tmp.png')
$saved = $false
try {
  $srcAspect = $img.Width / [double]$img.Height
  $tAspect = $TargetW / [double]$TargetH
  if ($srcAspect -gt $tAspect) {
    $newW = [int]($img.Height * $tAspect)
    $newH = $img.Height
    $x = [int](($img.Width - $newW)/2)
    $y = 0
  } else {
    $newW = $img.Width
    $newH = [int]($img.Width / $tAspect)
    $x = 0
    $y = [int](($img.Height - $newH)/2)
  }

  $cropRect = New-Object System.Drawing.Rectangle($x,$y,$newW,$newH)
  $destRect = New-Object System.Drawing.Rectangle(0,0,$TargetW,$TargetH)


  $canvas = New-Object System.Drawing.Bitmap($TargetW,$TargetH,[System.Drawing.Imaging.PixelFormat]::Format32bppArgb)
  $g = [System.Drawing.Graphics]::FromImage($canvas)
  $g.CompositingQuality = [System.Drawing.Drawing2D.CompositingQuality]::HighQuality
  $g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
  $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
  $g.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
  $g.DrawImage($img, $destRect, $cropRect, [System.Drawing.GraphicsUnit]::Pixel) | Out-Null
  $g.Dispose()

  $canvas.Save($tmp, [System.Drawing.Imaging.ImageFormat]::Png)
  $canvas.Dispose()
  $saved = $true
}
finally {
  $img.Dispose()
}

if ($saved -and (Test-Path $tmp)) {
  if (Test-Path $Source) { Remove-Item $Source -Force }
  Rename-Item $tmp $Source -Force
}

(Get-Item $Source) | Select-Object Name, Length
