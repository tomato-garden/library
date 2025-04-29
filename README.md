# 도서관 관리 시스템 with 외 않되요요?

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

## 프로젝트 개요
이 프로젝트는 도서관의 도서 대출, 반납, 관리를 위한 웹 기반 시스템입니다. 사용자와 관리자의 편의성을 고려하여 개발되었습니다.

## 주요 기능
- 사용자 관리 (회원가입, 로그인, 비밀번호 변경)
- 도서 검색 및 상세 정보 조회
- 도서 대출 및 반납
- 관심 도서 등록 및 관리
- 공지사항 게시 및 조회
- 관리자 기능 (도서 등록, 공지사항 관리)

## 기술 스택
- Frontend: HTML, CSS
- Backend: PHP
- Database: MySQL

## 프로젝트 구조
```
library/
├── admin.html        # 관리자 페이지
├── book_detail.php   # 도서 상세 정보
├── db.php           # 데이터베이스 연결
├── login.html       # 로그인 페이지
├── main.php         # 메인 페이지
├── mypage.php       # 마이페이지
├── notice.html      # 공지사항 페이지
├── register.html    # 회원가입 페이지
└── uploads/         # 업로드 파일 저장
```

## 설치 및 실행 방법
1. 웹 서버(Apache, Nginx 등) 설치
2. PHP 설치
3. MySQL 데이터베이스 설정
4. 프로젝트 파일을 웹 서버의 루트 디렉토리에 복사
5. `db.php` 파일에서 데이터베이스 연결 정보 설정
6. 웹 브라우저에서 접속

## 사용자 역할
- 일반 사용자: 도서 검색, 대출, 반납, 관심 도서 등록
- 관리자: 도서 등록, 공지사항 관리, 시스템 관리

## 이슈 제보
버그를 발견하거나 기능 개선을 제안하고 싶으시다면 [이슈](https://github.com/yourusername/library-management/issues)를 생성해주세요.

## 라이센스
이 프로젝트는 MIT 라이센스를 따릅니다. 자세한 내용은 [LICENSE](LICENSE) 파일을 참조하세요.

## 연락처처
프로젝트에 대한 질문이나 제안이 있으시면 이메일로 연락해주세요.(workship1211@gmail.com)
