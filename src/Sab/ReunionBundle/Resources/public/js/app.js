$(function () {

    cpt = 1;
    // Ajuster le scroller dashBoardBody
    var h = $('.dashBoardBody').height();
    var body_h = $('.body-app').height();
    var body_w = $('.body-app').width();

    if (h > 620) {
        $('#containerQuestion').height(h - 480);
    }
    if (body_w === 1024) {
        $('#containerQuestion').height(h - 480);
    }
    if (body_w <= 480) {
        $('.list-question').height(body_h - 260);
    }
    if (body_w <= 320) {
        $('.list-question').height(body_h - 100);
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
    $('.menu').on('click', menu);


    $('.addQuestion').on('click', ouvrirPopUp);
    $('.addQuestionMobile').on('click', ouvrirPopUp);
    $('.close-mobile').on('click', closePopUp);

    //tester si les champs sont pas vide
    $('#form_add_question').submit(function () {
        if ($('#sab_reunion_bundle_question_type_contenu').val() === "") {
            return false;
        }
    });

});

// Mettre le nombre de "dislike" d'une question à jours
var updateDislike = function (val_this) {

    var DataName = val_this.getAttribute('data-name');
    var IdEvent = val_this.getAttribute('data-event');

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

    var DataName = val_this.getAttribute('data-name');
    var IdEvent = val_this.getAttribute('data-event');

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
                        'background-position': '0px -64px'
                    });
                }
                //changer l'image apres le dislike
                if (nameAction === "updateDisLike") {
                    $(".disLikebutton[data-name=" + id_question + "]").addClass("dislike-disabled");
                    $(".disLikebutton[data-name=" + id_question + "]").attr("onclick", "false");
                    $(".disLikebutton[data-name=" + id_question + "]").css({
                        'background-position': '0px -91px'
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
                    'background-position': '0px -64px'
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
                    'background-position': '0px -91px'
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

//------------------------------------menu responsive------------------------------------------------------------

var menu = function () {
    if (cpt % 2 != 0) {
        $('.infoEvent').show();
        $('.menu span').removeClass('glyphicon-menu-hamburger');
        $('.menu span').addClass('glyphicon-remove');

        $('#containerQuestion').css("margin-top", "-7px");

    } else {
        var body_w = $('.body-app').width();
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

var ouvrirPopUp = function () {
    $('#ModalAddQuestion').css({'top': '85px', 'opacity': '1', 'overflow': 'inherit', 'display': 'inline'});
}

var closePopUp = function () {
    $('#ModalAddQuestion').css({'top': '0px', 'opacity': '0', 'overflow': 'hidden', 'display': 'none'});
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



