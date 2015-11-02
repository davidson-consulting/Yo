$(function () {


    cpt = 1;

    //ajuster le scroller dashBoardBody
    var h = $('.dashBoardBody').height();
    var body_h = $('.body-app').height();
    var body_w = $('.body-app').width();

    if (h > 676) {
        $('#containerQuestion').height(h - 480);
    }
    if (body_w === 1024) {
        $('#containerQuestion').height(h - 480);
    }
    if (body_w <= 480) {
        $('.list-question').height(body_h - 260);
    }
    if (body_w <= 320) {
//        $('.list-question').height(400);
        $('.list-question').height(body_h - 100);
    }


    //if list-question is empty
    var size_liste = $('.list-question section').size();
    if (size_liste == 0) {
        $('.emptyQuestion').show();
        $('.list-question .emptyQuestion').html("Aucune question n'a été posée");
    }

    //if msg info is empty
    setTimeout(function () {
        $('.msgInfo').hide(1000);
    }, 5000);

    limiterLike("", "init", "", true);

    $('.likebutton').on('click', updateLike);
    $('.disLikebutton').on('click', updateDislike);
    $('.menu').on('click', menu);

});

var updateDislike = function (val_this) {

//    val_this.attr("disabled","true");
    var DataName = val_this.getAttribute('data-name');
    var IdEvent = val_this.getAttribute('data-event');


    $.ajax({
        type: "POST",
        url: Routing.generate('_user_disliker_question', {'id': DataName}),
        success: function (result) {
            $(".nbDisLikeValue_" + DataName).html(result);
            var objet = {
                nameAction: "updateDisLike",
                idEvent: IdEvent,
                id_question: DataName,
                nb_dislike: result
            }
            // write datat in session storage
            write_session_storage("update_dislike_", DataName, objet);
            decrementeLesDislikes(DataName, result);
        },
        error: function () {
            console.log("error");
        },
        complete: function () {
            console.log("complete");
//            val_this.attr("disabled","false");
        }
    });
    return false;
}


var updateLike = function (val_this) {

    var DataName = val_this.getAttribute('data-name');
    var IdEvent = val_this.getAttribute('data-event');

    $.ajax({
        type: "POST",
        url: Routing.generate('_user_liker_question', {'id': DataName}),
        success: function (result) {
            $(".nbLikeValue_" + DataName).html(result);
            var objet = {
                nameAction: "updateLike",
                idEvent: IdEvent,
                id_question: DataName,
                nb_like: result
            }
            //write datat in session storage
            write_session_storage("update_like_", DataName, objet);
            decrementeLesLikes(DataName, result);
        },
        error: function () {
            console.log("error");
        },
        complete: function () {
            console.log("complete");
        }
    });
    return false;
}

//write in session storage
var write_session_storage = function (name, DataName, objet) {
    var objet_json = JSON.stringify(objet);
    localStorage.setItem(name + DataName, objet_json);
}

// verifier les likes et les dislikes
function limiterLike(id, name, nb, init) {
    for (i = 0; i <= localStorage.length - 1; i++) {
        key = localStorage.key(i)
        mydatas = localStorage.getItem(key);

        if (mydatas !== "none" && mydatas !== "block") {
            var read_objet = JSON.parse(mydatas);
            var id_question = read_objet.id_question;
            var id_event = read_objet.idEvent;
            var nameAction = read_objet.nameAction;

            if (init && name == "init") {
                if (nameAction === "updateLike") {
                    //changer l'image apres le like
                    $(".likebutton[data-name=" + id_question + "]").addClass("like-disabled");
                    $(".likebutton[data-name=" + id_question + "]").attr("onclick", "false");
                    $(".likebutton[data-name=" + id_question + "]").css({
                        'background-position': '0px -64px'
                    });
                } else {
                    //changer l'image apres le dislike
                    $(".disLikebutton[data-name=" + id_question + "]").addClass("dislike-disabled");
                    $(".disLikebutton[data-name=" + id_question + "]").attr("onclick", "false");
                    $(".disLikebutton[data-name=" + id_question + "]").css({
                        'background-position': '0px -91px'
                    }
                    );
                }
            }
        }
    }
}

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
                console.log("il exist dans le tableau");
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
                $(".likebutton[data-name=" + id_question + "]").attr("onclick", "false");
                $(".likebutton[data-name=" + id_question + "]").addClass("like-disabled");
                //changer l'image apres le like
                $(".likebutton[data-name=" + id_question + "]").css({
                    'background-position': '0px -64px'
                }
                );

            }
        }
    }
}

function decrementeLesDislikes(id, nb) {
    for (i = 0; i <= localStorage.length - 1; i++) {
        key = localStorage.key(i)
        mydatas = localStorage.getItem(key);

        if (mydatas !== "none" && mydatas !== "block") {

            var read_objet = JSON.parse(mydatas);

            var id_question = read_objet.id_question;
            var id_event = read_objet.idEvent;
            var nameAction = read_objet.nameAction;

            //if dislike question exist je dislike
            if ((nameAction === "updateLike") && (id == id_question)) {
                console.log("il exist dans le tableau");
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
                $(".disLikebutton[data-name=" + id_question + "]").attr("onclick", "false");
                $(".disLikebutton[data-name=" + id_question + "]").addClass("dislike-disabled");

                //changer l'image apres le dislike
                $(".disLikebutton[data-name=" + id_question + "]").css({
                    'background-position': '0px -91px'
                }
                );

            }
        }
    }
}

//---------------------------------------- ReadFile PDF ----------------------------------------------------------

function readFile() {

    if (cpt % 2 != 0) {
        $('#displayDoc').show();
        $('.etat').html("Arrêter la présentation");
        $('.block-read-presentation span.glyphicon').removeClass('glyphicon-play');
        $('.block-read-presentation span.glyphicon').addClass('glyphicon-stop');
    } else {
        $('#displayDoc').hide();
        $('.etat').html("Lire la présentation");
        $('.block-read-presentation span.glyphicon').removeClass('glyphicon-stop');
        $('.block-read-presentation span.glyphicon').addClass('glyphicon-play');
    }
    cpt++;
}



//------------------------------------menu resposive------------------------------------------------------------
var menu = function () {
    if (cpt % 2 != 0) {
        console.log("ouvert");
        $('.infoEvent').show();
        $('.menu span').removeClass('glyphicon-menu-hamburger');
        $('.menu span').addClass('glyphicon-remove');

        $('#containerQuestion').css("margin-top", "-7px");

    } else {
        var body_w = $('.body-app').width();
        console.log("fermer");
        $('.infoEvent').hide();
        $('.menu span').addClass('glyphicon-menu-hamburger');
        $('.menu span').removeClass('glyphicon-remove');
        if (body_w <= 320) {
            $('#containerQuestion').css("margin-top", "-12px");
        } else {
        $('#containerQuestion').css("margin-top", "-56px");
        }
    }
    cpt++;
}




//----------------------------------------le décompte avant le début de l'événement-----------------------------------

diffDateTimeEvent();

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
            days.html("<strong>" + d + " jours </strong>");
        } else {
            days.html("<strong>" + d + " jour </strong>");
        }
        s -= d * 86400;

        var h = Math.floor(s / 3600);
        if (h > 1) {
            hour.html("<string>" + h + " heures </strong>");
        } else {
            hour.html("<string>" + h + " heure </strong>");
        }
        s -= h * 3600;

        var m = Math.floor(s / 60);
        if (m > 1) {
            minute.html("<string>" + m + " minutes</strong>");
        } else {
            minute.html("<string>" + m + " minute</strong>");
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
        fin.html("<strong>L'événement est en cours</strong>");
        return false;
    }
    return false;
}



