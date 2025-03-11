# Emodia

![Image](https://github.com/user-attachments/assets/6cb7af06-b211-4030-bb4e-fc9d0e6b3088)

## **“개인의 감정에 맞는 미디어를 추천해주는 표정 인식 웹사이트”**

카메라 또는 사진을 사용하여 사용자의 표정을 인식한 후 입력받은 정보를 바탕으로 감정에 따른 미디어를 추천해주는 개인 프로젝트

## 🛠️ 구현 기술
- 백엔드 : PHP, MySQL
- 프론트엔드 : HTML5, CSS, JavaScritp
- 머신러닝 : Teachable Machine
- IDE : Visual Studio Code
- 배포 : iWinV

## 💻 주요 기능

### 📌 로그인 / 회원가입

- Token과 Cookie 이용한 로그인 유지 기능 구현
- 정규표현식 활용하여 아이디, 비밀번호, 이메일 유효성 검사
- 아이디  중복 검사
- 로그인 에러시 각 상황에 맞는 에러 메세지 출력
- 아이디, 비밀번호를 이메일 발송을 활용한 방식을 찾기

### 📌 메인 페이지

- 카메라 사용 및 이미지 삽입 구현
- ChatGPT API를 사용한 AI 미디어 추천 도우미 구현
- 이미지를 분석하여 가장 높은 감정값을 보여주고, 각 감정별 값을 퍼센트로 표시를 구현
- 개인에 맞는 영화를 추천해주고, 정보 사이트로 이동을 구현
- 개인에 맞는 음악을 추천해주고, 유튜브API를 사용하여 바로 재생 가능하도록 구현

### 📌 영화 검색 페이지

- 등록되어 있는 영화를 보여주고, 검색 가능
- 해당 영화의 평점을 누르면 평점 등록 페이지로 이동
- 평점 등록 페이지에서 평점을 등록하고 수정이 가능

### 📌 장바구니 페이지

- 상품 수량 변경 및 삭제 구현
- 선택한 상품에 대해서만 주문 진행할 수 있도록 구현
- 개별 상품 주문 기능 구현

### 📌 마이페이지

- 이메일 수정 기능 구현
- 비밀번호 변경 기능 구현
- 감정에 따른 선호 장르 변경 구현
- 회원 탈퇴 구현

### 📌 상품 등록 / 관리 페이지

- 판매자의 전체 상품에 대한 CRUD 기능 구현
- 각 상품 클릭시 해당 상품의 디테일 페이지로 이동 구현

### 📌 공통 기능

- 반응형 디자인 구현(PC, Mobile)
- Top 버튼 구현
