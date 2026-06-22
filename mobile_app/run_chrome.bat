@echo off
cd /d "%~dp0"
echo Starting Flutter app on Chrome...
echo API default: http://localhost/society/public/api/v1
echo Ensure Laragon Apache is running.
flutter pub get
REM If port busy: taskkill /F /IM dart.exe  OR use --web-port=7358
flutter run -d chrome --web-port=7357
