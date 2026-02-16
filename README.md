# LaraShop - Laravel 기반 한국형 쇼핑몰

Laravel 11 + Livewire + Tailwind CSS로 구축된 풀스택 온라인 쇼핑몰 프로젝트입니다.

## 기술 스택

- **Backend:** PHP 8.3, Laravel 11
- **Frontend:** Tailwind CSS, Alpine.js
- **실시간 UI:** Livewire 3
- **데이터베이스:** SQLite (개발) / MySQL (프로덕션)
- **인증:** Laravel Breeze

## 주요 기능

### 프론트 쇼핑몰
- 홈페이지 (추천상품, 신상품, 베스트셀러, 카테고리)
- 상품 목록 (필터링, 정렬, 페이지네이션)
- 상품 상세 (이미지 갤러리, 옵션 선택, 리뷰)
- 장바구니 (Livewire 실시간 업데이트)
- 위시리스트
- 체크아웃/주문 완료
- 마이페이지 (주문내역, 프로필 관리)
- 쿠폰 시스템
- 상품 검색 (자동완성)

### 관리자 패널
- 대시보드 (매출 통계, 최근 주문)
- 상품 관리 (CRUD, 이미지, 옵션)
- 카테고리 관리 (계층 구조)
- 주문 관리 (상태 변경)
- 쿠폰 관리
- 리뷰 관리 (승인/반려)
- 사이트 설정

### SEO / 성능
- SeoService를 통한 메타태그 관리
- JSON-LD 구조화 데이터 (Product, Organization, WebSite, BreadcrumbList, ItemList)
- Open Graph / Twitter Card 메타태그
- XML Sitemap
- robots.txt
- Google Analytics 연동
- 네이버/구글 사이트 인증
- 데이터베이스 인덱스 최적화
- 카테고리 메뉴 캐싱
- Lazy Loading 이미지
- N+1 쿼리 방지

### 기타
- 커스텀 에러 페이지 (404, 403, 500)
- 정적 페이지 (이용약관, 개인정보처리방침, 배송안내)
- 반응형 디자인 (모바일/태블릿/데스크탑)

## 프로젝트 구조

```
app/
  Enums/          - OrderStatus
  Http/
    Controllers/
      Admin/      - 관리자 컨트롤러 7개
      Shop/       - 프론트 컨트롤러 7개
  Livewire/Shop/  - Livewire 컴포넌트 7개
  Models/         - Eloquent 모델 15개
  Services/       - CartService, CouponService, OrderService, SeoService
  View/Components/- Seo 블레이드 컴포넌트
resources/views/
  admin/          - 관리자 뷰
  shop/           - 쇼핑몰 뷰
  components/     - 블레이드 컴포넌트
  errors/         - 에러 페이지
  layouts/        - 레이아웃 (shop, admin, app, guest)
  pages/          - 정적 페이지
  sitemap/        - XML 사이트맵
```

## 설치 방법

### 요구사항
- PHP 8.2+
- Composer
- Node.js 18+
- NPM

### 설치

```bash
# 1. 저장소 클론
git clone <repository-url> larashop
cd larashop

# 2. Composer 의존성 설치
composer install

# 3. 환경 설정
cp .env.example .env
php artisan key:generate

# 4. 데이터베이스 설정 (SQLite)
touch database/database.sqlite
php artisan migrate

# 5. 스토리지 링크
php artisan storage:link

# 6. 프론트엔드 빌드
npm install
npm run build

# 7. 개발 서버 실행
php artisan serve
```

### 관리자 계정 생성

```bash
php artisan tinker
```

```php
App\Models\User::create([
    'name' => '관리자',
    'email' => 'admin@larashop.kr',
    'password' => bcrypt('password'),
    'is_admin' => true,
    'email_verified_at' => now(),
]);
```

## 배포

```bash
# 프로덕션 배포 스크립트 실행
chmod +x deploy.sh
./deploy.sh
```

또는 수동으로:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

## 환경 변수

주요 설정은 관리자 패널의 '설정' 메뉴에서 관리합니다:
- 사이트명, 설명
- 고객센터 정보
- 무료배송 기준
- Google Analytics ID
- 네이버/구글 사이트 인증 코드

## 라이선스

이 프로젝트는 학습/데모 목적으로 제작되었습니다.
Laravel 프레임워크는 [MIT 라이선스](https://opensource.org/licenses/MIT) 하에 배포됩니다.
