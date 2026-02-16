#!/bin/bash

# ============================================
# LaraShop 프로덕션 배포 스크립트
# ============================================

set -e

echo "=========================================="
echo "  LaraShop 배포 시작"
echo "=========================================="

# 1. 유지보수 모드 활성화
echo "[1/10] 유지보수 모드 활성화..."
php artisan down --retry=60

# 2. 최신 코드 가져오기
echo "[2/10] 최신 코드 가져오기..."
git pull origin main

# 3. Composer 의존성 설치 (프로덕션)
echo "[3/10] Composer 의존성 설치..."
composer install --no-dev --optimize-autoloader --no-interaction

# 4. NPM 빌드
echo "[4/10] 프론트엔드 빌드..."
npm ci
npm run build

# 5. 데이터베이스 마이그레이션
echo "[5/10] 데이터베이스 마이그레이션..."
php artisan migrate --force

# 6. 캐시 최적화
echo "[6/10] 캐시 최적화..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan icons:cache 2>/dev/null || true

# 7. 스토리지 링크
echo "[7/10] 스토리지 링크 확인..."
php artisan storage:link 2>/dev/null || true

# 8. 큐 재시작
echo "[8/10] 큐 워커 재시작..."
php artisan queue:restart

# 9. 스케줄러 캐시 클리어
echo "[9/10] 애플리케이션 캐시 정리..."
php artisan cache:clear

# 10. 유지보수 모드 해제
echo "[10/10] 유지보수 모드 해제..."
php artisan up

echo ""
echo "=========================================="
echo "  LaraShop 배포 완료!"
echo "=========================================="
echo ""
echo "배포 시간: $(date '+%Y-%m-%d %H:%M:%S')"
echo ""
