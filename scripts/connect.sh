#!/bin/bash

# SSH Connection Script for Dukafy Server
# Usage: ./connect.sh

# Server Credentials
HOST="82.25.120.158"
PORT="22"
USER="u689745589"

# Connect via SSH
echo "Connecting to $HOST on port $PORT..."
echo "Username: $USER"
echo ""

ssh -p $PORT $USER@$HOST

# ssh -p 22 u689745589@82.25.120.158