$(function () {

    cpt = 1;
    // Ajuster le scroller dashBoardBody
    var h = $('.dashBoardBody').height();
    var body_h = $('.body-app').height();
    var body_w = $('.body-app').width();
    
    if (body_h <=640 || body_w <= 570) {

            calcDist();

    }

    //Tester si la list-question est vide (empty)
    var size_liste = $('.list-question section').size();
    if (size_liste == 0) {
        $('.emptyQuestion').show();
        $('.list-question .emptyQuestion').html("Aucune question n'a été posée");
    }

    //Tester si le message info est vide (empty)
    setTimeout(function () {
        $('.msgInfo').hide(1000);
    }, 5000);

    limiterLike("", "init", "", true);

    $('.likebutton').on('click', updateLike);
    $('.disLikebutton').on('click', updateDislike);
    // $('.menu').on('click', menu);


    $('.addQuestion').on('click', ouvrirPopUp);
    $('.addQuestionMobile').on('click', ouvrirPopUp);
    $('.close-mobile').on('click', closePopUp);

    //tester si les champs sont pas vide
    $('#form_add_question').submit(function () {
        if ($('#sab_reunion_bundle_question_type_contenu').val() === "") {
            return false;
        }
    });


$(window).scroll(function(){
      if ($(this).scrollTop() > 300) {
          $('.infoEventDiv').addClass('fixed');
      } else {
          $('.infoEventDiv').removeClass('fixed');
      }
  });


  diffDateTimeEvent();  

if (body_w <= 320){
  var previousScroll = 0,
    headerOrgOffset = $('.header').height();
    containerQuestion = $('.containerQuestion').height();
    list_question = $('.list-question').height();

// $('.header-wrap').height($('.header').height());

$(".list-question").scroll(function () {
    var currentScroll = $(this).scrollTop();
    if (currentScroll > headerOrgOffset) {
        if (currentScroll > previousScroll) {
            $('.header-wrap').slideUp();
            $('#show_question_title').css('margin-top', '30px');
            $('.list-question').height(body_h*110/100);
            $('.containerQuestion').height(body_h*110/100);
            $( "#menuMobile" ).removeClass( "in" );
        } else {
            $('.header-wrap').slideDown();
            $('#show_question_title').css('margin-top', '0px');
             $('.list-question').height(list_question);
             $('.containerQuestion').height(containerQuestion);
        }
    } 
    previousScroll = currentScroll;
});

}

if (body_w <= 480 && body_w > 320){
  var previousScroll = 0,
    headerOrgOffset = $('.headerMobile').height();
    containerQuestion = $('.containerQuestion').height();
    list_question = $('.list-question').height();

// $('.header-wrapMobile').height($('.headerMobile').height());

$(".list-question").scroll(function () {
    var currentScroll = $(this).scrollTop();
    if (currentScroll > headerOrgOffset) {
        if (currentScroll > previousScroll) {
            $('.header-wrapMobile').slideUp();
            $('#show_question_title').css('margin-top', '30px');
            $('.list-question').height(body_h*80/100);
            $('.containerQuestion').height(body_h*80/100);
            $( "#menuMobile" ).removeClass( "in" );


        } else {
            $('.header-wrapMobile').slideDown();
            $('#show_question_title').css('margin-top', '0px');
             $('.list-question').height(list_question);
             $('.containerQuestion').height(containerQuestion);

        }
    } 
    previousScroll = currentScroll;
});
}
});

function calcDist(){
    console.log("calcul de distance");
    var h = $('.dashBoardBody').height();
    var body_h = $('.body-app').height();
    var body_w = $('.body-app').width();
    console.log("h="+h);
    console.log("body_h="+body_h);
    console.log("body_w="+body_w);
    if (h > 620 && h<=799) {
        h1 = h*50/100;
        console.log("cas20");
        $('.containerQuestion').height(h1);
        $('.list-question').height(h1);
        $('.modal-body-commentaire').css('max-height', '600px');
    }
    else if (h > 799 && h <=900) {
        h1 = h*40/100;
        console.log("cas21");
        $('.containerQuestion').height(h1);
        $('.modal-body-commentaire').css('max-height', '600px');
    }else if (h > 900) {
        h1 = h*45/100;
        console.log("cas22");
        $('.containerQuestion').height(h1);
        $('.modal-body-commentaire').css('max-height', '600px');
    }

    // if (body_w === 1024) {
    //     console.log("cas3");
    //     $('.containerQuestion').height(h - 480);
    // }
    // else if ((body_w <= 480) && (body_w > 320)) {
    //     body_h1 = body_h*60/100;
    //     console.log("cas4");
    //     $('.list-question').height(body_h1);
    //     $('.modal-body-commentaire').css('max-height', '500px');
    // }
    // else if (body_w <= 320) {
    //     console.log("cas5");
    //     $('.list-question').height(body_h - 160);

    //     $('.modal-body-commentaire').css('max-height', '400px');
    // }
}


function addCommentInModal(idQuestion,parentCommentId){
   
     $('#loadingmessage').show();
    $.ajax({
        type: "POST",
        url: Routing.generate('_user_ajouter_commentaire', {'question_id' : idQuestion,'parentCommentId' :parentCommentId}),
        success: function (result) {

            // console.log("resulat ajax : " +result);
            $("#form_commentaire_"+idQuestion +" .modal-body-form").html(result);

        },
        error: function () {
            console.log("error");
        },
        complete: function () {
             $('#loadingmessage').hide();
            show_hide_form(this,idQuestion);
//            val_this.attr("disabled","false");
        }
    });

}


var updateDislike = function (val_this) {

    var DataName = $(val_this).attr('data-name');
    console.log(DataName);  
    var IdEvent = $(val_this).attr('data-event');

    $(".disLikebutton[data-name=" + DataName + "]").attr("onclick", "false");
    $(".disLikebutton[data-name=" + DataName + "]").addClass("dislike-disabled");

    $.ajax({
        type: "POST",
        url: Routing.generate('_user_disliker_question', {'id': DataName}),
        success: function (result) {
            $(".nbDisLikeValue_" + DataName).html(result);
            var date = new Date();
            var month = date.getMonth() + 1;
            function addZero(i) {
                if (i < 10) {
                    i = "0" + i;
                }
                return i;
            }
            var objet = {
                nameAction: "updateDisLike",
                idEvent: IdEvent,
                id_question: DataName,
                nb_dislike: result,
                background_displike: '0px -64px',
                background_like: '0px 0px',
                date: date.getFullYear() + "-" + month + "-" + date.getDate() + " " + date.getHours() + ":" + date.getMinutes() + ":" + addZero(date.getSeconds())
            }
            // write datat in session storage
            
            decrementeLesDislikes(DataName, result);
            write_session_storage("update_like_", DataName, objet);
            decrementeLesDislikes(DataName, result);
        },
        error: function () {
            console.log("");
        },
        complete: function () {
            console.log("");
        }
    });
    return false;
}

// Mettre le nombre de "like" d'une question à jour
var updateLike = function (val_this) {

    var DataName = $(val_this).attr('data-name');
    var IdEvent = $(val_this).attr('data-event');

    $(".likebutton[data-name=" + DataName + "]").attr("onclick", "false");
    $(".likebutton[data-name=" + DataName + "]").addClass("like-disabled");

    $.ajax({
        type: "POST",
        url: Routing.generate('_user_liker_question', {'id': DataName}),
        success: function (result) {
            $(".nbLikeValue_" + DataName).html(result);
            var date = new Date();
            var month = date.getMonth() + 1;
            function addZero(i) {
                if (i < 10) {
                    i = "0" + i;
                }
                return i;
            }
            var objet = {
                nameAction: "updateLike",
                idEvent: IdEvent,
                id_question: DataName,
                nb_like: result,
                background_like: '0px -64px',
                background_displike: '0px 0px',
                date: date.getFullYear() + "-" + month + "-" + date.getDate() + " " + date.getHours() + ":" + date.getMinutes() + ":" + addZero(date.getSeconds())
            }
            //write datat in session storage
            decrementeLesLikes(DataName, result);
            write_session_storage("update_like_", DataName, objet);
            decrementeLesLikes(DataName, result);
        },
        error: function () {
            console.log("");
        },
        complete: function () {
            console.log("");
        }
    });
    return false;
}

// Write in session storage (local storage)
var write_session_storage = function (name, DataName, objet) {
    var objet_json = JSON.stringify(objet);
    localStorage.setItem(name + DataName, objet_json);
}

// Limiter les likes et les dislikes - Un likes / dislike par utilisateur pour une seul question !
function limiterLike(id, name, nb, init) {
    for (i = 0; i <= localStorage.length - 1; i++) {
        key = localStorage.key(i)
        mydatas = localStorage.getItem(key);

        if (mydatas !== "none" && mydatas !== "block") {
            var read_objet = JSON.parse(mydatas);
            var id_question = read_objet.id_question;
            var id_event = read_objet.idEvent;
            var nameAction = read_objet.nameAction;
            var background_dis = read_objet.background_displike;
            var background_like = read_objet.background_like;
            var date_modif = read_objet.date;

            if (init && name == "init") {
                if (nameAction === "updateLike") {
                    //changer l'image apres le like
                    $(".likebutton[data-name=" + id_question + "]").addClass("like-disabled");
                    $(".likebutton[data-name=" + id_question + "]").attr("onclick", "false");
                    $(".likebutton[data-name=" + id_question + "]").css({
                        'background-position': '0px -60px'
                    });
                }
                //changer l'image apres le dislike
                if (nameAction === "updateDisLike") {
                    $(".disLikebutton[data-name=" + id_question + "]").addClass("dislike-disabled");
                    $(".disLikebutton[data-name=" + id_question + "]").attr("onclick", "false");
                    $(".disLikebutton[data-name=" + id_question + "]").css({
                        'background-position': '0px -94px'
                    });
                }
            }
        }
    }
}


//Décrementer le nombre de like d'une question
function decrementeLesLikes(id, nb) {
    for (i = 0; i <= localStorage.length - 1; i++) {
        key = localStorage.key(i)
        mydatas = localStorage.getItem(key);

        if (mydatas !== "none" && mydatas !== "block") {

            var read_objet = JSON.parse(mydatas);

            var id_question = read_objet.id_question;
            var id_event = read_objet.idEvent;
            var nameAction = read_objet.nameAction;

            //if dislike question exist je dislike
            if ((nameAction === "updateDisLike") && (id == id_question)) {
                //requette ajax pour decremente le dislike param : id_question, nb = nb de dislikes;
                $.ajax({
                    type: 'POST',
                    url: Routing.generate('_user_decremente_liker_question', {'id': id_question}),
                    success: function (result) {
                    },
                    error: function () {
                    },
                    complete: function () {
                    }
                });
            }

            if ((id == id_question)) {
                //$(".likebutton[data-name=" + id_question + "]").attr("onclick", "false");
                //$(".likebutton[data-name=" + id_question + "]").addClass("like-disabled");
                //changer l'image apres le like
                $(".likebutton[data-name=" + id_question + "]").css({
                    'background-position': '0px -60px'
                }
                );
                //dislike
                $(".disLikebutton[data-name=" + id_question + "]").css({
                    'background-position': '0px -34px'
                }
                );
            }
        }
    }
}


//Décrementer le nombre de dislike d'une question
function decrementeLesDislikes(id, nb) {
    for (i = 0; i <= localStorage.length - 1; i++) {
        key = localStorage.key(i)
        mydatas = localStorage.getItem(key);

        if (mydatas !== "none" && mydatas !== "block") {
            var read_objet = JSON.parse(mydatas);
            var id_question = read_objet.id_question;
            var id_event = read_objet.idEvent;
            var nameAction = read_objet.nameAction;

            //Tester si la question est "liké" alors on la dislike sinon on incrémente juste les dislikes
            if ((nameAction === "updateLike") && (id == id_question)) {
                //requette ajax pour decremente les likes param : id_question, nb = nbr_de like;
                $.ajax({
                    type: 'POST',
                    url: Routing.generate('_user_decremente_disliker_question', {'id': id_question}),
                    success: function (result) {
                    },
                    error: function () {
                    },
                    complete: function () {
                    }
                });
            }

            if ((id == id_question)) {
                //$(".disLikebutton[data-name=" + id_question + "]").attr("onclick", "false");
                //$(".disLikebutton[data-name=" + id_question + "]").addClass("dislike-disabled");

                //changer l'image apres le dislike
                $(".disLikebutton[data-name=" + id_question + "]").css({
                    'background-position': '0px -94px'
                }
                );
                //like
                $(".likebutton[data-name=" + id_question + "]").css({
                    'background-position': '0px 0px'
                });

            }
        }
    }
}

var comment =function(){

    
}

//---------------------------------------- ReadFile PDF ----------------------------------------------------------

//function readFile() {
//
//    if (cpt % 2 != 0) {
//        $('#displayDoc').show();
//        $('.etat').html("Arrêter la présentation");
//        $('.block-read-presentation span.glyphicon').removeClass('glyphicon-play');
//        $('.block-read-presentation span.glyphicon').addClass('glyphicon-stop');
//    } else {
//        $('#displayDoc').hide();
//        $('.etat').html("Lire la présentation");
//        $('.block-read-presentation span.glyphicon').removeClass('glyphicon-stop');
//        $('.block-read-presentation span.glyphicon').addClass('glyphicon-play');
//    }
//    cpt++;
//}



//------------------------------------menu resposive------------------------------------------------------------
// var menu = function () {
//     if (cpt % 2 != 0) {
//         $('.infoEvent').show();
//         $('.menu span').removeClass('glyphicon-menu-hamburger');
//         $('.menu span').addClass('glyphicon-remove');

//         $('.containerQuestion').attr('style', 'margin-top: -7px !important');

//     } else {
//         var body_w = $('.body-app').width();
//         $('.infoEvent').hide();
//         $('.menu span').addClass('glyphicon-menu-hamburger');
//         $('.menu span').removeClass('glyphicon-remove');
//         if (body_w <= 320) {
//             $('.containerQuestion').attr('style', 'margin-top: -12px !important');
//         } else {
//             $('.containerQuestion').attr('style', 'margin-top: -56px !important');
//         }
//     }
//     cpt++;
// }

var ouvrirPopUp = function () {
    $('#ModalAddQuestion').css({'top': '85px', 'opacity': '1', 'overflow': 'inherit', 'display': 'inline'});
}

var closePopUp = function () {
    $('#ModalAddQuestion').css({'top': '0px', 'opacity': '0', 'overflow': 'hidden', 'display': 'none'});
}


//----------------------------------------le décompte avant le début de l'événement-----------------------------------


// $(".btnMenu").on('click',diffDateTimeEventMobile);


function diffDateTimeEvent() {

    var dateDebutEvent = $('.dateDebutEvent').html();
    var dateNow = new Date();
    var dateDebut = Date.parse(dateDebutEvent);
    var days = $('#days');
    var hour = $("#hour");
    var minute = $('#minute');
    var seconds = $("#seconds");
    var fin = $("#fin").hide();

    var s = (dateDebut.getTime() - dateNow.getTime()) / 1000;
    var d = Math.floor(s / 86400);
    if (d >= 0) {
        if (d > 1) {
            days.html("<strong color='#F7491D'>" + d + " jours </strong>");
        } else {
            days.html("<strong color='#F7491D'>" + d + " jour </strong>");
        }
        s -= d * 86400;

        var h = Math.floor(s / 3600);
        if (h > 1) {
            hour.html("<strong color='#F7491D'>" + h + " heures </strong>");
        } else {
            hour.html("<strong color='#F7491D'>" + h + " heure </strong>");
        }
        s -= h * 3600;

        var m = Math.floor(s / 60);
        if (m > 1) {
            minute.html("<strong color='#F7491D'>" + m + " minutes</strong>");
        } else {
            minute.html("<strong color='#F7491D'>" + m + " minute</strong>");
        }
        s -= m * 60;
        s = Math.floor(s);

        if (s > 1) {
//            seconds.html("<string>" + s + " secondes</strong>");
        } else {
//            seconds.html("<string>" + s + " seconde</strong>");
        }
        setTimeout(diffDateTimeEvent, 1000);
        return true;
    } else {
        days.hide();
        hour.hide();
        minute.hide();
        seconds.hide();
        fin.show();
        fin.html("<strong color='#606060'>L'événement est en cours</strong>");
        return false;
    }
    return false;
}

function diffDateTimeEventMobile() {

    var dateDebutEvent = $('.dateDebutEvent').html();
    var dateNow = new Date();
    var dateDebut = Date.parse(dateDebutEvent);
    var days = $('#daysMobile');
    var hour = $("#hourMobile");
    var minute = $('#minuteMobile');
    var seconds = $("#secondsMobile");
    var fin = $("#finMobile").hide();

    var s = (dateDebut.getTime() - dateNow.getTime()) / 1000;
    var d = Math.floor(s / 86400);
    if (d >= 0) {
        if (d > 1) {
            days.html("<strong color='#F7491D'>" + d + " jours </strong>");
        } else {
            days.html("<strong color='#F7491D'>" + d + " jour </strong>");
        }
        s -= d * 86400;

        var h = Math.floor(s / 3600);
        if (h > 1) {
            hour.html("<strong color='#F7491D'>" + h + " heures </strong>");
        } else {
            hour.html("<strong color='#F7491D'>" + h + " heure </strong>");
        }
        s -= h * 3600;

        var m = Math.floor(s / 60);
        if (m > 1) {
            minute.html("<strong color='#F7491D'>" + m + " minutes</strong>");
        } else {
            minute.html("<strong color='#F7491D'>" + m + " minute</strong>");
        }
        s -= m * 60;

        s = Math.floor(s);

        if (s > 1) {
//            seconds.html("<string>" + s + " secondes</strong>");
        } else {
//            seconds.html("<string>" + s + " seconde</strong>");
        }
        setTimeout(diffDateTimeEvent, 1000);
        return true;
    } else {
        days.hide();
        hour.hide();
        minute.hide();
        seconds.hide();
        fin.show();
        fin.html("<strong color='#606060'>L'événement est en cours</strong>");
        return false;
    }
    return false;
}


function show_hide_form(bouton, questionId){
    var form_commentaireElt = $('#form_commentaire_'+questionId).css('display');
    if (form_commentaireElt=="none"){
        $('#form_commentaire_'+questionId).show();
    }
    else{
        $('#form_commentaire_'+questionId).hide();
    }
}

