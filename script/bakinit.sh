#!/bin/bash
GREEN='\033[0;32m'
NC='\033[0m'

echo -e "${GREEN}欢迎使用数据库备份脚本初始化工具${NC}"
echo "请输入以下信息："

read -p "数据库名: " DB_NAME
read -p "数据库用户名: " DB_USER
read -s -p "数据库密码: " DB_PASS
echo ""
read -p "远程备份站地址 (例: https://bak.123.com): " REMOTE_HOST
read -p "希望保留的备份数量（例如30）: " KEEP_COUNT

TEMPLATE_PATH="$(dirname "$0")/db_auto_backup_push.sh.template"
TARGET_DIR="/www/server/panel/script"
TARGET_SCRIPT="$TARGET_DIR/db_auto_backup_push.sh"

mkdir -p "$TARGET_DIR"
mkdir -p "/www/backup/db"

sed -e "s|{{DB_NAME}}|$DB_NAME|g" \
    -e "s|{{DB_USER}}|$DB_USER|g" \
    -e "s|{{DB_PASS}}|$DB_PASS|g" \
    -e "s|{{REMOTE_HOST}}|$REMOTE_HOST|g" \
    -e "s|{{KEEP_COUNT}}|$KEEP_COUNT|g" \
    "$TEMPLATE_PATH" > "$TARGET_SCRIPT"

chmod +x "$TARGET_SCRIPT"

echo -e "${GREEN}✅ 成功生成脚本：$TARGET_SCRIPT${NC}"
