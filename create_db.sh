#!/bin/bash

# Prompt user for MySQL username and password
read -p "Enter MySQL username: " username
read -sp "Enter MySQL password: " password
echo ""

# MySQL command to create database
create_db_query="CREATE DATABASE IF NOT EXISTS kejamove_test_pos;"

# Execute MySQL command
mysql -u "$username" -p"$password" -e "$create_db_query"

echo "Database created successfully."
