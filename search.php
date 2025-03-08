<?php
session_start();
if (!isset($_SESSION["id"])) {
    echo "<script> alert('로그인이 필요합니다.');
        location.replace('login.php'); </script>";
    exit;
}

$conn = mysqli_connect('localhost', '', '', '')
    or die("데이터베이스 연결 오류");

// 페이지네이션 함수
function generate_pagination($current_page, $total_pages, $visible_pages = 5)
{
    $pagination = array();
    $half_visible = floor($visible_pages / 2);

    $start_page = max(1, $current_page - $half_visible);
    $end_page = min($total_pages, $current_page + $half_visible);

    if ($end_page - $start_page < $visible_pages - 1) {
        if ($start_page == 1) {
            $end_page = min($visible_pages, $total_pages);
        } elseif ($end_page == $total_pages) {
            $start_page = max(1, $total_pages - $visible_pages + 1);
        }
    }

    if ($start_page > 1) {
        $pagination[] = 1;
        if ($start_page > 2) {
            $pagination[] = '...';
        }
    }

    for ($page = $start_page; $page <= $end_page; $page++) {
        $pagination[] = $page;
    }

    if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) {
            $pagination[] = '...';
        }
        $pagination[] = $total_pages;
    }

    return $pagination;
}

// 페이지 번호 가져오기 (기본값 1)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

$where = "";
if (isset($_GET["search"]) && $_GET["search"] != "") {
    $search = mysqli_real_escape_string($conn, $_GET["search"]);
    $where = "WHERE no LIKE '%$search%' OR title LIKE '%$search%' OR year LIKE '%$search%' OR genre LIKE '%$search%' OR rating LIKE '%$search%'";
}

// 전체 아이템 수 가져오기
$count_query = "SELECT COUNT(*) as total FROM movie $where";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_items = $count_row['total'];

// 전체 페이지 수 계산
$total_pages = ceil($total_items / $items_per_page);

// 쿼리 수정
$query = "SELECT * FROM movie $where ORDER BY no ASC LIMIT $items_per_page OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>영화 검색</title>
    <link rel="stylesheet" type="text/css" href="./css/search.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    <script src="https://d3js.org/d3.v6.min.js"></script>
    <script src="./js/search.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Jua&display=swap" rel="stylesheet">
</head>

<body>
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-logo">
                <a href="index.php">Emodia</a>
            </div>
            <button class="navbar-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
            <ul class="navbar-menu">
                <li><a href="search.php">영화 검색</a></li>
                <?php if (isset($_SESSION["id"])): ?>
                    <li><a href="mypage.php" class="user-id">마이페이지</a></li>
                    <li><a href="logout.php">로그아웃</a></li>
                <?php else: ?>
                    <li><a href="login.php">로그인</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="search-container">
        <form name="search" method="get" action="search.php">
            <input type="text" name="search"
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <input type="submit" value="검색">
        </form>
    </div>

    <div class="movie-table">
        <div>*평점을 눌러 평점을 줄 수 있습니다.</div>
        <table>
            <thead>
                <th>번호</th>
                <th>제목</th>
                <th>년도</th>
                <th>장르</th>
                <th>평점</th>
                <th>포스터</th>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td data-label="번호"><?php echo $row["no"]; ?></td>
                        <td data-label="제목"><?php echo $row["title"]; ?></td>
                        <td data-label="년도"><?php echo $row["year"]; ?></td>
                        <td data-label="장르"><?php echo $row["genre"]; ?></td>
                        <td data-label="평점"><a href='rate_movie.php?movie_id=<?php echo $row['no']; ?>' class='rating-link'>
                                <?php echo number_format($row['rating'], 1); ?></a></td>
                        <td data-label="포스터">
                            <?php if (!empty($row["poster"]) && $row["poster"] != "NULL"): ?>
                                <img src="<?php echo $row["poster"]; ?>" alt="Movie Poster">
                            <?php else: ?>
                                포스터 없음
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination">
        <?php
        $pagination_links = generate_pagination($page, $total_pages);

        $search_param = isset($_GET['search']) ? urlencode($_GET['search']) : '';

        // 이전 페이지 링크
        if ($page > 1) {
            echo "<a href='?page=" . ($page - 1) . "&search=$search_param'>&laquo; </a>";
        }

        // 페이지 번호 링크
        foreach ($pagination_links as $link) {
            if ($link === '...') {
                echo "<span class='ellipsis'>$link</span>";
            } elseif ($link == $page) {
                echo "<strong>$link</strong>";
            } else {
                echo "<a href='?page=$link&search=$search_param'>$link</a>";
            }
        }

        // 다음 페이지 링크
        if ($page < $total_pages) {
            echo "<a href='?page=" . ($page + 1) . "&search=$search_param'> &raquo;</a>";
        }
        ?>
    </div>
</body>

</html>

<?php
mysqli_close($conn);
?>