<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">




<link rel="stylesheet" href="./ui-themes/themes/smoothness/jquery-ui.css" />
<script src="./jquery-1.9.1.min.js"></script>
<script src="./jquery-ui-1.10.2/ui/jquery-ui.js"></script>
<link rel="stylesheet" href="./demos/style.css" />


  <style type='text/css'>

.draggable { width: 90px; height: 80px; padding: 5px; margin: 0 10px 10px 0; font-size: .9em; }
.ui-widget-header p, .ui-widget-content p { margin: 0; }
#map_grid { height: 1000px; width: 800px; float: left; border: 1px solid #000; background: url(./ui-themes/themes/smoothness/images/grid_bkg.png) repeat;}

 h1 { padding: .2em; margin: 0; }
#menu { float:left; width: 500px; margin-right: 2em; overflow: scroll; height: 800px; }
.sign_th { float: right; }
.drag { width: 75px; border-width: 1px; padding: 1px; margin: 1px; border-style: solid; float: right; }
.part { width: 75px; border-width: 1px; padding: 1px; margin: 1px; border-style: solid; background: #ffffff}

#trash {
    display:inline-block;
    width:50px;
    height:50px;
    border:1px solid greenyellow;
    background-color: #cd0a0a;
    padding:0px;
    margin: 20px auto;
}


</style>
<?php
$signs = array(
1 => array('name' => 'Start', 'desc' => 'Indicates the beginning of the course. Dog does not have
to be sitting at start.', 'type' => ''),
2 => array('name' => 'Finish', 'desc' => 'Indicates the end of the course–timing stops.', 'type' => ''),
3 => array('name' => 'HALT - Sit', 'desc' => 'While heeling, the handler halts and the dog sits in heel
position. The team then moves forward, with the dog in heel position.', 'type' => 'Stationary'),
4 => array('name' => 'HALT - Sit - Down', 'desc' => 'While heeling, the handler halts and the dog sits.  The handler then commands and/or signals the dog to down, followed by the command to heel forward from the down position.', 'type' => 'Stationar'),
5 => array('name' => 'Right Turn', 'desc' => 'Performed as a 90° turn to the right, as in traditional obedience.', 'type' => ''),
6 => array('name' => 'Left Turn', 'desc' => 'Performed as a 90° turn to the left, as in traditional obedience.', 'type' => ''),
7 => array('name' => 'About Turn - Right', 'desc' => 'While heeling, the team makes a 180° about turn to the handler’s right.', 'type' => ''),
8 => array('name' => 'About "U" Turn', 'desc' => 'While heeling, the team makes a 180° turn to the handler’s left.', 'type' => ''),
9 => array('name' => '270° Right Turn', 'desc' => 'While heeling, the team makes a 270° turn to the handler’s right.   270° turns are performed as a tight circle, but not around the exercise sign.', 'type' => ''),
10 => array('name' => '270° Left Turn', 'desc' => 'While heeling, the team makes a 270° turn to the handler’s left.   270° turns are performed as a tight circle, but not around the exercise sign.', 'type' => ''),
11 => array('name' => '360° Right Turn', 'desc' => 'While heeling, the team makes a 360° turn to the handler’s right.   360° turns are performed as a tight circle, but not around the exercise sign.', 'type' => ''),
12 => array('name' => '360° Left Turn', 'desc' => 'While heeling, the team makes a 360° turn to the handler’s left.   360° turns are performed as a tight circle, but not around the exercise sign.', 'type' => ''),
13 => array('name' => 'Call Front - Finish Right - Forward', 'desc' => 'While heeling, the handler stops forward motion and calls the dog to the front position (dog sits in front and faces the handler). The handler may take several steps backward as the dog turns and moves to sit in the front position.  Second part of the exercise directs the handler to command and/or signal the dog to change from the front position by moving to the handler’s right, around behind the handler, toward heel position. As the dog clears the handler’s path, the handler moves forward before the dog has completely returned to the heel position.  The dog does not sit before moving forward in heel position with the handler. (Stationary exercise) Handler must not step forward or backward to aid the dog as the dog moves toward heel position.', 'type' => 'Stationary'),
14 => array('name' => 'Call Front - Finish Left - Forward', 'desc' => 'While heeling, the handler stops forward motion and calls the dog to the front position (dog sits in front and faces the handler). The handler may take several steps backward as the dog turns and moves to sit in the front position.  Second part of the exercise directs the handler to command and/or signal the dog to change from the front position by moving to the handler’s left toward heel position. As the dog clears the handler’s path, the handler moves forward before the dog has completely returned to the heel position. The dog does not sit before moving forward in heel position with the handler. Handler must not step forward or backward to aid the dog as the dog moves toward heel position.', 'type' => 'Stationary'),
15 => array('name' => 'Call Front - Finish Right - HALT', 'desc' => 'While heeling, the handler stops forward motion and calls the dog to the front position (dog sits in front and faces the handler). The handler may take several steps backward as the dog turns and moves to sit in the front position. Second part is the finish to the right, where the dog must return to heel position by moving around the right side of the handler. Dog must sit in heel position before moving forward with the handler. Handler must not step forward or backward to aid the dog as the dog moves toward heel position.', 'type' => 'Stationary'),
16 => array('name' => 'Call Front - Finish Left - HALT', 'desc' => 'While heeling, the handler stops forward motion and calls the dog to the front position (dog sits in front and faces the handler). The handler may take several steps backward as the dog turns and moves to a sit in the front position.  Second part is the finish to the left, where the dog must return to heel position by moving around the left side of the handler and sit in heel position. Dog must sit in heel position before moving forward in heel position with the handler. Handler must not step forward or backward to aid the dog as the dog moves toward heel position.', 'type' => 'Stationary'), 
17 => array('name' => 'Slow Pace', 'desc' => 'Dog and handler must slow down noticeably. This must be followed by a normal pace unless it is the last station on the course.', 'type' => ''),
18 => array('name' => 'Fast Pace', 'desc' => 'Dog and handler must speed up noticeably. This must be followed by a normal pace.', 'type' => ''),
19 => array('name' => 'Normal Pace', 'desc' => 'Dog and handler must move forward, walking briskly and naturally. This station can only be used after a change of pace.', 'type' => ''),
20 => array('name' => 'Moving Side Step Right', 'desc' => 'While heeling, the handler takes one step to the right, leading with the right foot, and continues moving forward along the newly established line. The dog moves with the handler. The exercise shall be performed just before the exercise sign. (This exercise shall be considered a change of direction and the sign shall be placed directly in line with the handler’s path requiring the handler and dog to sidestep to the right to pass the sign.)', 'type' => ''),
21 => array('name' => 'Spiral Right – Dog Outside', 'desc' => 'This exercise requires three pylons or posts placed in a straight line with spaces between them of approximately 6-8 feet. Spiral Right indicates the   handler must turn to the right when moving around each pylon or post. This places the dog on the outside of the turns (see 1A and 1B). The exercise sign is placed near or on the first pylon or post where the spiral is started.', 'type' => ''),
22 => array('name' => 'Spiral Left – Dog Inside', 'desc' => 'This exercise requires three pylons or posts placed in a straight line with spaces between them of approximately 6-8 feet. Spiral Left indicates that the handler must turn to the left when moving around each pylon or post. This places the dog on the inside of the turns  (see 2). The exercise sign is placed near or on the first pylon or post where the spiral is started.', 'type' => ''),
23 => array('name' => 'Straight Figure 8 Weave Twice', 'desc' => 'This exercise requires four pylons or posts placed in a straight line with spaces between them of approximately 6-8 feet. The exercise sign is placed near or on the first pylon or post where the exercise is started. Entry into the weaving pattern is with the first pylon or post at the dog/handler’s left side. The dog and handler must complete the entire exercise by passing the last pylon or post.', 'type' => ''),
24 => array('name' => 'Serpentine Weave Once', 'desc' => 'This exercise requires pylons or posts placed in a straight line with spaces between them of approximately 6-8 feet. The exercise sign is placed near or on the first pylon or post where the exercise starts. Entry into the weaving pattern is with the first pylon or post at the dog/handler’s left side. The dog and handler must complete the entire exercise by passing the last pylon or post. It should be noted that in this exercise, the team does not weave back through the obstacles as they do in the Straight Figure 8.', 'type' => ''),
25 => array('name' => 'HALT–1, 2 and 3 Steps Forward', 'desc' => 'The handler halts and the dog sits in heel position to begin the exercise. The handler takes one step forward and halts with the dog maintaining heel position. The dog sits when the handler halts. This is followed by two steps forward– halt, and three steps forward–halt, with the dog heeling each time the handler moves forward and sitting each time the handler halts. (Stationary exercise)', 'type' => ''),
26 => array('name' => 'Call Front–1, 2 and 3 Steps Backward', 'desc' => 'While heeling, the handler stops forward motion and calls the dog to the front position (dog sits in front and faces the handler). The handler may take several steps backward as the dog turns and moves to a sit in the front position. With the dog in the front position, the handler takes one step backward and halts. The dog moves with the handler and sits in the front position as the handler halts. This is followed by the handler taking two steps backward and a halt, and three steps backward and a halt. Each time, the dog moves with the handler to the front position and sits as the handler halts. The handler then commands and/or signals the dog to resume heel position. When returning to the heel position, the dog does not sit before the handler moves forward.', 'type' => 'Stationary'),
27 => array('name' => '2Down and Stop', 'desc' => 'While moving with the dog in heel position, the handler commands and/or signals the dog to down as the handler comes to a stop next to the dog. Once the dog is completely down, the handler moves forward, commanding the dog to move forward from down position. (Stationary exercise)', 'type' => ''),
28 => array('name' => 'HALT–Fast Forward From Sit', 'desc' => 'The handler halts and the dog sits in heel position. With the dog sitting in heel position, the handler commands and/or signals the dog to heel and immediately moves forward at a fast pace. This must be followed by a normal pace.', 'type' => 'Stationary'),
29 => array('name' => 'Left About Turn', 'desc' => 'While moving with the dog in heel position, the handler makes an about turn to the left, while at the same time, the dog must move around the handler to the right and into heel position. The dog does not sit before moving forward in heel position with the handler.', 'type' => ''),
30 => array('name' => 'HALT and Walk Around Dog', 'desc' => 'Handler halts and dog sits. With the dog sitting in heel position, the handler commands and/or signals the dog to stay, then proceeds to walk around the dog to the left, returning to heel position. The handler must pause in heel position before moving forward to the next station.', 'type' => 'Stationary'),
31 => array('name' => 'HALT–Down–Walk Around Dog', 'desc' => 'Handler halts and dog sits. With the dog sitting in heel position, the handler commands and/or signals the dog to down and stay, then proceeds to walk around the dog to the left, returning to heel position. The handler must pause in heel position before moving forward to the next station. The dog heels forward from the down position.', 'type' => 'Stationary'),
32 => array('name' => 'Figure 8 – No Distractions', 'desc' => 'Two pylons or posts spaced approximately 6-8 feet apart.  The team enters the sequence with the posts on either left or right and will perform a complete figure 8 around the posts or pylons, crossing the center point three times. (see 3A and 3B). ', 'type' => ''),
33 => array('name' => 'HALT – Left Turn – Forward', 'desc' => 'Handler halts, dog sits.  With the dog sitting the handler commands and/or signals the dog to heel, as the handler turns to the left and continues to move forward in the new direction without hesitation.  The dog must turn with handler as the handler turns.', 'type' => 'Stationary'),
34 => array('name' => 'HALT – Right Turn – Forward', 'desc' => 'Handler halts, dog sits.  With the dog sitting the handler commands and/or signals the dog to heel, as the handler turns to the right and continues to move forward in the new direction without hesitation.  The dog must turn with the handler as the handler turns.', 'type' => 'Stationary'),
35 => array('name' => 'Call Front – Return to Heel', 'desc' => 'While heeling the handler stops forward motion and calls the dog to the front position. The handler may take several steps backward as the dog turns and moves to sit in the front position. Dog sits in front and faces the handler. The handler will then walk around behind the dog and return to the heel position and pause.  Dog must remain sitting as handler walks around dog.  (This is a 180˚ change of direction, about turn.)', 'type' => 'Stationary'),
36 => array('name' => 'Halt–Slow Forward From Sit', 'desc' => 'The handler halts, and the dog sits in heel position. The handler then commands and/or signals the dog to heel and moves forward at a slow pace. The dog must maintain heel position as handler slowly moves forward. This must be followed by a normal pace, unless it is the last station on the course.', 'type' => 'Stationary'),
101 => array('name' => 'HALT–About Turn Right and Forward', 'desc' => 'Handler halts and dog sits. With the dog sitting in heel position, the team turns 180° to the right and immediately moves forward.', 'type' => 'Stationary'),
102 => array('name' => 'HALT–About “U” Turn and Forward', 'desc' => 'Handler halts and dog sits. With the dog sitting in heel position, the team turns 180° to the left and immediately moves forward.', 'type' => 'Stationary'),
103 => array('name' => 'Send Over Jump–Handler Passes By', 'desc' => 'While moving with the dog in heel position, the handler directs the dog to take the jump as the handler passes by the jump without any pause, hesitation or stopping. When the dog has completed the jump in the proper direction, it is called to heel position and the team continues to the next exercise.', 'type' => ''),
104 => array('name' => 'HALT–Turn Right One Step–Call to Heel– Halt', 'desc' => 'Handler halts and dog sits. With the dog sitting, the handler commands and/or signals the dog to stay. The handler then turns to the right, while taking one step in that direction, and halts. The dog is directed to heel position and must move and sit in the new location before moving forward to the next station.', 'type' => 'Stationary'),
105 => array('name' => 'HALT–Stand Dog–Walk Around', 'desc' => 'Handler halts and dog sits. With the dog sitting in heel position, the handler stands the dog and commands and/or signals the dog to stay as the handler walks around the dog to the left, returning to heel position. The handler must pause in heel position before moving forward to the next station. In the Advanced class, the handler may touch the dog, move forward to stand the dog, and may pose the dog as in the show ring.', 'type' => 'Stationary'),
106 => array('name' => 'HALT–90° Pivot Right–HALT', 'desc' => 'Handler halts and dog sits. With the dog sitting in heel position, the handler pivots 90° to the right and halts. The dog moves with the handler and sits in heel position.', 'type' => 'Stationary'),
107 => array('name' => 'HALT–90° Pivot Left–HALT', 'desc' => 'Handler halts and dog sits. With the dog sitting in heel position, the handler pivots 90° to the left and halts. The dog moves with the handler and sits in heel position.', 'type' => 'Stationary'),
108 => array('name' => 'Offset Figure 8', 'desc' => 'This exercise requires two pylons or posts placed about 8–10 feet apart, around which the team will perform a complete Figure 8, crossing the center line three times. Two distractions will be arranged to the sides of the Figure 8 about 5–6 feet apart. Entry may be between the pylons or posts and the distraction on either side. (see 3A and 3B). The distractions will consist of two securely covered containers with tempting dog treats; however, dog toys may replace one or both containers, or may be placed next to the containers. The exercise sign may be placed on or near the cone where entry is made into the Offset Figure 8.  Pylons or posts may not be shared with other exercises.', 'type' => ''),
109 => array('name' => '', 'desc' => 'Handler halts in front of the station sign and the dog sits. With the dog sitting in heel position, the handler moves one step directly to the right and halts. The dog moves with the handler and sits in heel position when the handler halts. The exercise shall be performed just before the exercise sign. This exercise shall be considered a change of direction and the sign shall be placed directly in line with the handler’s path, requiring the handler and dog to sidestep to the right to pass the sign.', 'type' => 'Stationary'),
110 => array('name' => 'HALT–Call Dog Front–Finish Right', 'desc' => 'Handler halts and dog sits. With the dog sitting in heel position, the handler calls the dog to front and the dog sits in the front position, facing the handler. On command, the dog then moves from the front position around the right of the handler and sits in heel position. Handler must not step forward or backward to aid the dog during the exercise.', 'type' => 'Stationary'),
111 => array('name' => 'HALT–Call Dog Front–Finish Left', 'desc' => 'Handler halts and dog sits. With the dog sitting in heel position, the handler calls the dog to front and the dog sits in the front position facing the handler. On command, the dog then moves to the handler’s left and sits in heel position. Handler must not step forward or backward to aid dog during exercise.', 'type' => 'Stationary'),
112 => array('name' => 'HALT–180° Pivot Right–HALT', 'desc' => 'Handler halts and dog sits. With the dog sitting in heel position, the handler pivots 180° to the right and halts. The dog moves with the handler and sits in heel position.', 'type' => 'Stationary'),
113 => array('name' => 'HALT–180° Pivot Left–HALT', 'desc' => 'Handler halts and dog sits. With the dog sitting in heel position, the handler pivots 180° to the left and halts. The dog moves with the handler and sits in heel position.', 'type' => 'Stationary'),
114 => array('name' => 'HALT–Down–Sit', 'desc' => 'Handler halts and dog sits. With dog sitting in heel position, the handler commands and/or signals the dog to down, then to sit.', 'type' => 'Stationary'),
115 => array('name' => 'HALT – Stand', 'desc' => 'Handler halts and dog sits.  With the dog sitting in heel position, the handler will stand the dog.  Handler then resumes heel position while the dog stands in place. Handler pauses before moving forward. In the Advanced class, the handler may touch the dog, move forward to stand the dog and may pose the dog as in the show ring.  Handler may not touch the dog in the Excellent Class, but may move forward to stand the dog and may pose the dog as in the show ring.', 'type' => 'Stationary'),
116 => array('name' => 'Halt–Pivot Right–Forward', 'desc' => 'The handler halts and the dog sits in heel position. The handler commands and/or signals the dog to heel, then pivots to the right and dog and handler move forward.', 'type' => 'Stationary'),
117 => array('name' => 'Halt–Pivot Left–Forward', 'desc' => 'The handler halts and the dog sits in heel position. The handler commands and/or signals the dog to heel, then pivots to the left and dog and handler move forward.', 'type' => 'Stationary'),
118 => array('name' => 'HALT - Leave Dog–2 Steps–Call to Heel– Forward', 'desc' => 'The handler halts, and the dog sits in heel position. While the dog remains sitting the handler takes two steps forward and pauses.   The handler moves forward and commands the dog to resume heel position.  The dog must move briskly.', 'type' => 'Stationary'),
201 => array('name' => 'HALT–Stand–Down', 'desc' => 'Handler halts and dog sits. With dog sitting in heel position, the handler will stand the dog (without physical handling or moving forward), then command and/or signal the dog to down. The handler then commands and/or signals the dog to heel forward from the down position.', 'type' => 'Stationary'),
202 => array('name' => 'HALT–Stand–Sit', 'desc' => 'Handler halts and dog sits. With dog sitting in heel position, the handler will stand the dog (without physical handling or moving forward), then command and/or signal the dog to sit. The handler then commands and/or signals the dog to heel forward from the sitting position.', 'type' => 'Stationary'),
203 => array('name' => 'Moving stand–Walk around dog', 'desc' => 'While heeling and without pausing, the handler will stand the dog and walk around the dog to the left, returning to heel position. The handler must pause in heel position after returning to the dog. Dog must move forward from the standing position.', 'type' => ''),
204 => array('name' => 'Moving down–Walk around dog', 'desc' => 'While heeling and without pausing, the handler will down the dog and walk around the dog to the left, returning to heel position. The handler must pause in heel position after returning to the dog. The dog must move forward from the down position.', 'type' => ''),
205 => array('name' => 'Backup 3 steps', 'desc' => 'While heeling, the handler reverses direction walking backward at least 3 step, without first stopping, then continues heeling forward. The dog moves backward with the handler and maintains heel position throughout the exercise without sitting.', 'type' => ''),
206 => array('name' => 'Down While Heeling', 'desc' => 'While moving forward, without pause or hesitation, the handler will command and/or signal the dog to down and stay as the handler continues forward about 6 feet to the Call marker.  The handler will turn and face the dog, pause and then command and/or signal the dog to heel.  This is a 180˚ change of direction, about turn. (This sign will be followed within 6 feet by the Call marker.)  Dog must return to heel position and sit, the handler must pause before moving forward.', 'type' => 'Stationary'),
207 => array('name' => 'Stand While Heeling', 'desc' => 'While moving forward, without pause or hesitation the handler will command and/or signal the dog to stand and stay as the handler continues forward about 6 feet to the Call marker.  The handler will turn and face the dog, pause and then command and/or signal the dog to heel.  This is a 180˚ change of direction, about turn.  (This sign will be followed within 6 feet by the Call marker.)  Dog must return to heel position and sit, the handler must pause before moving forward.', 'type' => 'Stationary'),
208 => array('name' => 'Stand – Leave Dog – Sit Dog – Call Front – Finish', 'desc' => 'While heeling, the handler will stop and command and/or signal the dog to stand. The dog must stand and stay without sitting first.  Then the handler will walk forward approximately 6 feet to the Call marker. The handler will turn to face the dog and command and/or signal the dog to sit.  When the dog sits, the handler will command and/or signal the dog to front.  The dog sits in the front position facing the handler.  On command and/or signal, the dog will move to heel position.  Dog must sit in heel position before moving forward with the handler. (Stationary Exercise) (This exercise reverses the direction of the team.)', 'type' => ''),
209 => array('name' => 'Stand – Leave Dog – Down Dog – Call Front – Finish', 'desc' => 'While heeling, the handler will stop and stand the dog using a command and/or signal, then walk forward approximately 6 feet to the Call marker.  The dog must stand and stay without sitting first.  The handler will turn to face the dog and command and/or signal the dog to down.  When the dog downs, the handler will command and/or signal the dog to front.  The dog must sit in the front position facing the handler.  On command and/or signal, the dog will move to heel position.  The dog must sit in heel position before moving forward with the handler.  (Stationary Exercise) (This exercise reverses the direction of the team.)', 'type' => ''),
210 => array('name' => 'Send to Jump', 'desc' => 'At the sign for this station, the handler will command and/or signal the dog to leave heel position to execute the jump.    The dog must leave the handler immediately and execute the jump.  The handler must maintain a straight path of at least a 3 foot distance away from the jump and may not pass the jump until the dog has returned to heel position.  The dog must jump the jump in the proper direction and return to heel position without pause, hesitation, or stopping.  The team then continues to the next station.', 'type' => ''),
211 => array('name' => 'Double Left About Turn', 'desc' => 'While moving with the dog in heel position, the handler makes an about turn to the left while at the same time, the dog must move around the handler to the right and into heel position. The handler must take one or two steps forward before performing the exercise a second time. The handler will end up turning 360° to the left as the dog turns 360° to the right around the handler. The dog does not sit at any time during this exercise.', 'type' => ''),
298 => array('name' => 'Sit Stay', 'desc' => 'This sign will be used as a marker for the sit stay exercise.  The dog must remain in the sit position while the handler retrieves the leash and returns to heel position and the judge says “exercise finished.”', 'type' => ''),
299 => array('name' => 'Call', 'desc' => 'This sign will be used as a marker for associated exercises.', 'type' => '')
);
?>

<script type='text/javascript'>//<![CDATA[ 
$(window).load(function(){
var inserite = 0;

$('.drop').droppable({
    tolerance: 'fit',
    accept: '.drag, .part',
    drop: function (event, ui) {
        var top = ui.position.top;
        var left = ui.position.left;
        var posizione = ui.position;



        if(ui.helper.hasClass('drag') ) {
            var $part = ui.helper.clone().removeClass("drag ui-draggable-dragging").addClass("part");
            $part.appendTo($(this));
// Create handle dynamically
ele = $part;
// Create handle dynamically
$('<div></div>').appendTo(ele).attr('id','handle').css({
    'position': 'absolute',
    'bottom': 60,
    'right': 5,
    'height': 26,
    'width': 26,
    'background-color': 'orange'
});


var src = $('.part img').attr('src').split('/');
var file = src[src.length - 1];


            $part.draggable({
                tolerance: 'fit',
                revert: 'invalid', //resterà non valido finchè non droppo in un punto valido
                containment: "#map_grid",
                stop: function () {
                    var top2 = ui.position.top;
                    var left2 = ui.position.left;
                    $("#map_grid").append('<div>spostata</div>');
                    $(this).draggable('option', 'revert', 'invalid'); //quando lo lascio torna indietro perche' il revert è invalid'


                }
            });
            $part.droppable({
                greedy: true,
                //tolerance: 'intersect',
                drop: function (event, ui) {
                    if (!$(ui.helper).hasClass('part')) {
                        ui.draggable.draggable('option', 'revert', true);



                    }
                }
            });
        };
        inserite++;
        $("#map_grid").append('<div>' + top + '-' + left + '--' + inserite + file + '</div>');
    }
});
$('#trash').droppable({
    tolerance: 'touch',
    accept: "#map_grid > .part",
    drop: function (event, ui) {
        deleteImage(ui.draggable);
        $("#map_grid").append('<div>eliminata</div>');
    }
});


$('.drag').draggable({
    revert: 'invalid',
    helper: 'clone',
    stop: function () {
        $(this).draggable('option', 'revert', 'invalid');
    }
});

$('.drag').droppable({
    tolerance: 'touch',
    containment: '#map_grid',
    drop: function (event, ui) {
        ui.draggable.draggable('option', 'revert', true);


    }
});

$( "#signs" ).accordion({
heightStyle: "content"
});

function deleteImage($item) {
    $item.remove();
};
});//]]>  





</script>

</head>
<body>
	


<div id="menu">
<h1 class="ui-widget-header">Novice</h1>
<div id="signs" class="nodrop">

<?php foreach ($signs as $id => $data) {

	
echo '<h2><a href="#">'.$id.' '.$data['name'].'</a></h2>';
echo '<div  id="drag'.$id.'"><div class="drag"><img class="sign_th" src="signs/thumbs/signs_2012-'.$id.'-th.jpg"></div>';
echo $data['desc'];

echo '</div>';
} ?>

</div>
</div>



<div id="map_grid" class="ui-widget-header drop">
</div>


</body>
</html>