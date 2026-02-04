@echo off
title AI Ad System - Auto Runner

echo ==========================================
echo 1. UPLOADING FACEBOOK ADS...
echo ==========================================
python upload_facebook.py
if %errorlevel% neq 0 goto error

echo.
echo ==========================================
echo 2. UPLOADING GOOGLE ADS...
echo ==========================================
python upload_google.py
if %errorlevel% neq 0 goto error

echo.
echo ==========================================
echo 3. RUNNING AI BRAIN...
echo ==========================================
python ad_brain.py
if %errorlevel% neq 0 goto error

echo.
echo ==========================================
echo ✅ SUCCESS! ALL TASKS COMPLETED.
echo ==========================================
pause
exit

:error
echo.
echo ❌ A FATAL ERROR OCCURRED. STOPPING.
pause