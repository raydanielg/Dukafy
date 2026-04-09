#!/bin/bash

# Upload Build Files to Server
# This script uploads the public/build folder to the production server

HOST="82.25.120.158"
PORT="65002"
USER="u689745589"
REMOTE_PATH="~/domains/ematokeo.ac.tz/public_html/public/build"
LOCAL_PATH="public/build"

echo "================================"
echo "  Upload Build to Server"
echo "================================"
echo "Server: $HOST:$PORT"
echo "User: $USER"
echo "Local: $LOCAL_PATH"
echo "Remote: $REMOTE_PATH"
echo "================================"
echo ""

# Check if build folder exists
if [ ! -d "$LOCAL_PATH" ]; then
    echo "ERROR: Build folder not found at $LOCAL_PATH"
    echo "Please run 'npm run build' first"
    exit 1
fi

echo "Step 1: Creating remote directory..."
ssh -p $PORT $USER@$HOST "mkdir -p $REMOTE_PATH"

echo "Step 2: Uploading files..."
# Use tar for faster upload
tar -czf - -C public build | ssh -p $PORT $USER@$HOST "tar -xzf - -C ~/domains/ematokeo.ac.tz/public_html/public/"

if [ $? -eq 0 ]; then
    echo ""
    echo "SUCCESS! Build files uploaded."
else
    echo "ERROR: Upload failed"
fi
