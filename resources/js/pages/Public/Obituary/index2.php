<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body>
    <div class="bg-main w-[calc(100vw-20px)] 
        
        h-[100vh] z-index-[-10] fixed top-[0px] left-[0px]">
    <div class="relative flex w-[100%] h-[100%]">
    <div class="w-absolute top-0 left-0 w-[24%] h-[24%] bg-[url('/Memorial-18-01.webp')] bg-no-repeat bg-cover">1</div>
    <div class="absolute top-0 right-0 w-[24%] h-[24%] bg-[url('/Memorial-18-01.webp')] bg-no-repeat bg-cover scale-x-[-1]">2</div>
    <div class="absolute bottom-0 left-0 w-[24%] h-[24%] bg-[url('/Memorial-18-01.webp')] bg-no-repeat bg-cover scale-y-[-1]">3</div>
    <div class="absolute bottom-0 right-0 w-[24%] h-[24%] bg-[url('/Memorial-18-01.webp')] bg-no-repeat bg-cover scale-x-[-1] scale-y-[-1]">4</div>
</div>






    </div>

    <div class="main w-[calc(100vw-20px)] min-h-[100vh] flex justify-center  z-index-[1] absolute top-[0px] left-[0px]">
    <img src="3.png" class="w-[1000px]" alt="">
    </div>
</body>
</html>