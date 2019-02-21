function blink(n){$(n).fadeOut("slow",function(){$(this).fadeIn("slow",function(){blink(this)})})}blink(".blink");
