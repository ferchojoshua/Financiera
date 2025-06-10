$Action = New-ScheduledTaskAction -Execute "$env:USERPROFILE\Desktop\Delete.Bat"
$Trigger = New-ScheduledTaskTrigger -Weekly -DaysOfWeek Tuesday -At 9PM
$Principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest
$Settings = New-ScheduledTaskSettingsSet -MultipleInstances Parallel
Register-ScheduledTask -TaskName "LimpiezaBackups" -Action $Action -Trigger $Trigger -Principal $Principal -Settings $Settings 