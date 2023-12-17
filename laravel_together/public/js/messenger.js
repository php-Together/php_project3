 // 초기 탭 설정
 document.getElementById('tab1').style.display = 'block';

 // 탭 열기 함수
 function openTab(tabId) {
     // 모든 탭 숨기기
     var tabs = document.getElementsByClassName('tab-content');
     for (var i = 0; i < tabs.length; i++) {
         tabs[i].style.display = 'none';
        }
        
        // 선택한 탭 보이기
        document.getElementById(tabId).style.display = 'block';
        
        // 활성화된 탭 스타일 적용
        var activeTabs = document.getElementsByClassName('tab');
        for (var i = 0; i < activeTabs.length; i++) {
            activeTabs[i].classList.remove('tab-active');
        }
        event.currentTarget.classList.add('tab-active');
    }
    
    // 모달 열기/닫기 함수
    function toggleModal() {
        var modal = document.getElementById('m-myModal');
        
     // 저장된 액티브 상태를 가져옴
     const lastActiveElement = sessionStorage.getItem('lastActiveElement');

    if (modal.style.display === 'none' || modal.style.display === '') {
        
        modal.style.display = 'block';

         // 모달이 열릴 때 기본으로 활성화할 요소에 active 클래스 추가
         if (lastActiveElement) {
            document.getElementById(lastActiveElement).classList.add('tab-active');
        }
        friendRequestList();
    } else {
        // 현재 액티브 상태를 저장
        const activeElement = document.querySelector('.tab-active');
        if (activeElement) {
            sessionStorage.setItem('lastActiveElement', activeElement.id); // 세션 스토리지에 저장
        }
        modal.style.display = 'none';
    }
}

// 모달 외부 클릭 시 닫기
window.onclick = function (event) {
    var modal = document.getElementById('m-myModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// 모달 열기 함수
function openModal() {
    document.getElementById('friend-Modal').style.display = 'block';
}
// 모달 닫기 함수
function mcloseModal() {
    document.getElementById('m-myModal').style.display = 'none';
}
// 모달 닫기 함수
function fcloseModal() {
    document.getElementById('friend-Modal').style.display = 'none';

    resetModal();
}

// 모달 메세지 초기화 함수
function resetModal() {
    messageContainer.innerHTML = '';  // 메세지 초기화
}
// ------------------------ 친구 요청 목록 -------------------------

// 모달이 열릴때 실행 되는 함수
function friendRequestList() {

    // AJAX를 통해 친구 요청 목록 가져오기
    fetch('/friendRequests')
        .then(response => {
            // 응답이 성공적인지 확인
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            // JSON 형식으로 변환하여 반환
            return response.json();
        })
        .then(data => {

            var friendRequests = data.friendRequests;
            var friendRequestCount = data.friendRequestCount;

            if (friendRequestCount === 0) {
                displayFriendRequests(['친구 요청이 없습니다.']);
            } else {
                displayFriendRequests(friendRequests, friendRequestCount);
            }
        })
        .catch(error => {
            // 오류 처리
            console.error('Fetch error:', error);
        });
        
}

function displayFriendRequests(friendRequests, friendRequestCount) {
    // friend-request-div
    var friendrequestdiv = document.getElementById('friend-request-div');
    var noticecount = document.getElementById('noticecount');

    // 기존 내용 초기화
    friendrequestdiv.innerHTML = '';

    if (friendrequestdiv) {
        // 받아온 친구 요청 목록을 모달 내부에 추가
        for (var i = 0; i < friendRequests.length; i++) {
            var friendRequest = friendRequests[i];

            noticecount.textContent = friendRequestCount;

            // friend-request-div > messenger-user-div, m-received-bg
            var userDiv = document.createElement('div');
            userDiv.classList.add('messenger-user-div', 'm-received-bg');

            friendrequestdiv.appendChild(userDiv);

            // friend-request-div > messenger-user-div, m-received-bg > user-profile 
            var userprofilediv = document.createElement('div');
            userprofilediv.classList.add('user-profile');

            userDiv.appendChild(userprofilediv);

            // 1. 이미지 추가
             var userprofileImg = document.createElement('img');
             userprofileImg.src = '/img/profile-img.png';

             userprofilediv.appendChild(userprofileImg);

            // 2. 이름 추가
            var username = document.createElement('p');
            username.classList.add('user-name');
            username.textContent = friendRequest.name;

            userDiv.appendChild(username);

            // 3. 이메일 추가
            var useremail = document.createElement('p');
            useremail.classList.add('user-email');
            useremail.textContent = friendRequest.email;

            userDiv.appendChild(useremail);

            // 4. 거절 버튼 추가
            var refusebtn =  document.createElement('button');
            refusebtn.classList.add('refuse-btn');
            refusebtn.textContent = '거절'

            userDiv.appendChild(refusebtn);

            // 5. 수락 버튼 추가
            var acceptbtn =  document.createElement('button');
            acceptbtn.classList.add('accept-btn');
            acceptbtn.textContent = '수락'

            userDiv.appendChild(acceptbtn);

            
        }
    } else {
        console.error('Element with id "friend-request-div" not found.');
    }
}

// ------------------------ 친구 요청 목록 -------------------------

// -------------------------- 친구 요청 ----------------------------
const submitBtn = document.getElementById('submitBtn');
const messageContainer = document.querySelector('.request-message');

submitBtn.addEventListener('click', function (event) {
    event.preventDefault(); // 서브밋 버튼의 기본 동작 방지

    const receiverEmail = document.getElementById('receiver_email').value; // 소문자로 변환;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    if (!receiverEmail.trim()) {
        messageContainer.innerHTML = '이메일을 입력하세요.';
        return;
    }

    // 이메일 형식 검사
    // 이메일 형식 확인을 위한 정규 표현식
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

     if (!emailRegex.test(receiverEmail)) {
        messageContainer.innerHTML = '올바른 이메일 형식이 아닙니다.';
        return;
    }

    fetch('/friendsend', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
            receiver_email: receiverEmail,
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 성공 메시지를 출력하고 모달은 열어둡니다.
            messageContainer.innerHTML = data.message;
            // 추가로 필요한 로직 수행...
        } else {
            // 에러 메시지를 출력하고 모달은 열어둡니다.
            messageContainer.innerHTML = data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
// -------------------------- 친구 요청 ----------------------------

