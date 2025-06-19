<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $breed = mysqli_real_escape_string($conn, $_POST['breed']);
    $age = (int)$_POST['age'];
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $listing_type = mysqli_real_escape_string($conn, $_POST['listing_type']);
    $price = $listing_type == 'buy_sell' ? mysqli_real_escape_string($conn, $_POST['price']) : null;
    $photo = $_FILES['photo']['name'];
    $photo_tmp = $_FILES['photo']['tmp_name'];

    if ($photo) {
        $photo_path = "../assets/images/uploads/" . basename($photo);
        move_uploaded_file($photo_tmp, $photo_path);
    } else {
        $photo_path = null;
    }

    $query = "INSERT INTO pets (user_id, name, breed, age, category, photo, location, listing_type, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "isssssssd", $user_id, $name, $breed, $age, $category, $photo_path, $location, $listing_type, $price);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: ../dashboard.php');
        exit();
    } else {
        $error = "Failed to add pet.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link
      rel="stylesheet"
      as="style"
      onload="this.rel='stylesheet'"
      href="https://fonts.googleapis.com/css2?display=swap&family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&family=Plus+Jakarta+Sans%3Awght%40400%3B500%3B700%3B800"
    />

    <title>PetHaven - Add Pet</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>

<body>
    <div class="relative flex size-full min-h-screen flex-col bg-white group/design-root overflow-x-hidden" style='font-family: "Plus Jakarta Sans", "Noto Sans", sans-serif;'>
      <div class="layout-container flex h-full grow flex-col">
        <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#f5f2f0] px-10 py-3">
          <div class="flex items-center gap-4 text-[#181511]">
            <div class="size-4">
              <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M24 4H6V17.3333V30.6667H24V44H42V30.6667V17.3333H24V4Z" fill="currentColor"></path>
              </svg>
            </div>
            <h2 class="text-[#181511] text-lg font-bold leading-tight tracking-[-0.015em]">PetHaven</h2>
          </div>
          <div class="flex flex-1 justify-end gap-8">
            <div class="flex items-center gap-9">
              <a class="text-[#181511] text-sm font-medium leading-normal" href="../index.php">Home</a>
              <a class="text-[#181511] text-sm font-medium leading-normal" href="../search.php?listing_type=adoption">Adopt</a>
              <a class="text-[#181511] text-sm font-medium leading-normal" href="../search.php?listing_type=buy_sell">Buy/Sell</a>
            </div>
            <div class="flex gap-2">
              <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../dashboard.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Dashboard</span>
                </a>
                <a href="../auth/logout.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Logout</span>
                </a>
              <?php else: ?>
                <a href="../auth/login.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Log in</span>
                </a>
                <a href="../auth/register.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Sign up</span>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </header>
        <div class="px-40 flex flex-1 justify-center py-5">
          <div class="layout-content-container flex flex-col w-[512px] max-w-[512px] py-5 max-w-[960px] flex-1">
            <h2 class="text-[#181511] tracking-light text-[28px] font-bold leading-tight px-4 text-center pb-3 pt-5">Add a New Pet</h2>
            <?php if (isset($error)): ?>
              <p class="text-[#ff0000] text-sm font-normal leading-normal px-4 text-center pb-3"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="POST" action="add_pet.php" enctype="multipart/form-data" class="flex flex-col gap-4 px-4 py-3">
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Name</p>
                <input
                  type="text"
                  name="name"
                  placeholder="Name"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                  required
                />
              </label>
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Breed</p>
                <input
                  type="text"
                  name="breed"
                  placeholder="Breed"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                />
              </label>
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Age</p>
                <input
                  type="number"
                  name="age"
                  placeholder="Age"
                  min="0"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                  required
                />
              </label>
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Category</p>
                <select
                  name="category"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 bg-[image:--select-button-svg] placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                  required
                >
                  <option value="dog">Dog</option>
                  <option value="cat">Cat</option>
                  <option value="other">Other</option>
                </select>
              </label>
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Location</p>
                <input
                  type="text"
                  name="location"
                  placeholder="Location"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                />
              </label>
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Listing Type</p>
                <select
                  id="listing_type"
                  name="listing_type"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 bg-[image:--select-button-svg] placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                  required
                >
                  <option value="adoption">Adoption</option>
                  <option value="buy_sell">Buy/Sell</option>
                </select>
              </label>
              <label id="price_field" class="flex flex-col min-w-40 flex-1 hidden">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Price (USD)</p>
                <input
                  type="number"
                  id="price"
                  name="price"
                  placeholder="Price"
                  min="0"
                  step="0.01"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                />
              </label>
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Photo</p>
                <input
                  type="file"
                  id="photo"
                  name="photo"
                  accept="image/*"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                />
              </label>
              <div class="flex gap-3">
                <button
                  type="submit"
                  class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]"
                >
                  <span class="truncate">Add Pet</span>
                </button>
                <a href="../dashboard.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Back to Dashboard</span>
                </a>
              </div>
            </form>
          </div>
        </div>
        <footer class="bg-[#f5f2f0] px-10 py-6 mt-auto">
          <div class="flex flex-wrap justify-between gap-6">
            <div>
              <h4 class="text-[#181511] text-sm font-bold leading-normal mb-2">Connect with Us</h4>
              <p class="text-[#8a7760] text-sm font-normal leading-normal">Email: <a href="mailto:pethaven@gmail.com" class="underline">pethaven@gmail.com</a></p>
            </div>
            <div>
              <h4 class="text-[#181511] text-sm font-bold leading-normal mb-2">Quick Links</h4>
              <p class="text-[#8a7760] text-sm font-normal leading-normal"><a href="../index.php" class="underline">Home</a></p>
              <p class="text-[#8a7760] text-sm font-normal leading-normal"><a href="../search.php?listing_type=adoption" class="underline">Adoption</a></p>
            </div>
            <div>
              <h4 class="text-[#181511] text-sm font-bold leading-normal mb-2">Follow Us</h4>
              <p class="text-[#8a7760] text-sm font-normal leading-normal"><a href="#" class="underline">Instagram</a></p>
            </div>
          </div>
        </footer>
      </div>
    </div>

    <script>
        document.getElementById('listing_type').addEventListener('change', function() {
            var priceField = document.getElementById('price_field');
            if (this.value === 'buy_sell') {
                priceField.classList.remove('hidden');
                document.getElementById('price').setAttribute('required', 'required');
            } else {
                priceField.classList.add('hidden');
                document.getElementById('price').removeAttribute('required');
            }
        });
    </script>
</body>

</html>