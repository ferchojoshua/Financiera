@echo off
echo %date% %time% - Iniciando limpieza de backups >> C:\DBBackups\cleanup_log.txt
IF EXIST "C:\DBBackups\*.*" (
    Del /Q C:\DBBackups\*.*
    echo %date% %time% - Archivos eliminados exitosamente >> C:\DBBackups\cleanup_log.txt
) ELSE (
    echo %date% %time% - No se encontraron archivos para eliminar >> C:\DBBackups\cleanup_log.txt
) 