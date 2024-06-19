#!/bin/sh

# подключаем инициализацию
PROGRAM="$0"
DIR=$(dirname "$(realpath "${PROGRAM}")")
export PROJECT_ROOT="$(dirname "$(dirname "$(realpath "$0")")")"