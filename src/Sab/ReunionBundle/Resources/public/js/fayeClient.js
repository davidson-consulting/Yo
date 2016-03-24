//recupèrer les clients qui sont connecter à l'application.
var client = new Faye.Client('http://localhost:8001/');


//------------Ajouter des questions--------------------------------------------------

//récupérer les clients qui sont connecter au tunnel "/addQuestion"
var supscription_add_question = client.subscribe('/addQuestion', function (data) {

    var idEventOld = $('.question:first .blockLike .likeButton .likebutton').data("event");
    var idEvent = data.datas.idEvent;
    var date = data.datas.datePublication.date;
    var auteur = "";
    var class_nbLikeValue = "nbLikeValue_" + data.datas.id;
    var class_nbDisLikeValue = "nbDisLikeValue_" + data.datas.id;

    if (data.datas.auteur === null) {
        auteur = "Anonyme";
    } else {
        auteur = data.datas.auteur;
    }

    if (idEventOld === idEvent) {
        $('.list-question').prepend("<section class='question q_"+data.datas.id+"'>\n\
                                <section class='blockLike'>\n\
                                <div class='nbLike'>\n\
                                <span class=" + class_nbLikeValue + ">" + data.datas.nbLikes + "</span>\n\
                                </div>\n\
                                <div class='likeButton'>\n\
                                <div class='likebutton' onclick='updateLike(this)' data-name='" + data.datas.id + "' data-event='" + data.datas.idEvent + "'></div>\n\
                                </div>\n\
                                <div class='nbDisLike'>\n\
                                <span class=" + class_nbDisLikeValue + ">" + data.datas.nbDisLikes + "</span>\n\
                                </div>\n\
                                <div class='likeButton'>\n\
                                <div class='disLikebutton' onclick='updateDislike(this)' data-name='" + data.datas.id + "' data-event='" + data.datas.idEvent + "'></div>\n\
                                </div>\n\
                                </section>\n\
                                <section class='blockContenuQuestion'>\n\
                                <div class='contenuQuestion'><p>" + data.datas.contenu + "</p></div>\n\
                                <section class='auteur'>\n\
                                 posté par : <span>" + auteur + " - </span>\n\
                                <span class='dataPublication'>" + date + "</span>\n\
                                </section>\n\
                                </section>\n\
                                </section>");
        $('.emptyQuestion').remove();
    }
});




//-------------Update likes----------------------------------------------------------------

//récupérer les clients qui sont connecter au tunnel "/updateLikes"
var supscription_update_like = client.subscribe('/updateLikes', function (data) {
    var class_nbLikeValue = "nbLikeValue_" + data.datas.id;
    $('.nbLikeValue_' + data.datas.id).replaceWith("<span class=" + class_nbLikeValue + ">" + data.datas.nbLikes + "</span>");
});

//-------------Update Dislikes-------------------------------------------------------------

//récupérer les clients qui sont connecter au tunnel "/updateDisLikes"
var supscription_update_dislike = client.subscribe('/updateDisLikes', function (data) {
    var class_nbDisLikeValue = "nbDisLikeValue_" + data.datas.id;
    $('.nbDisLikeValue_' + data.datas.id).replaceWith("<span class=" + class_nbDisLikeValue + ">" + data.datas.nbDisLikes + "</span>");
});


//------------update count question -------------------------------------------------------

//récupérer les clients qui sont connecter au tunnel "/updateCountQuestion"
client.subscribe("/updateCountQuestion", function (data) {
    var idEvent = data.idEvent;
    var count = data.count;
    var result;
    if (count == 0 || count == 1) {
        result = count + " Question";
    } else {
        result = count + " Questions";
    }
    $('.count_' + idEvent).replaceWith("<div class = info-event count_" + idEvent + ">" + result + "</div>");
    $('.count_admin_' + idEvent).replaceWith('<span class = "view-event-question-count count_admin_' + idEvent + ' badge">' + result + '</span>');
});


//-------------Question focus---------------------------------------

//récupérer les clients qui sont connecter au tunnel "/focusQuestion"
//Afficher à tous les utilisateurs la question 


client.subscribe('/focusQuestion', function (data) {

    var idEvent = data.datas.idEvent;
    var idQuestion = data.datas.idQuestion;
    var contenu = data.datas.contenu;
    var auteur = data.datas.auteur;
    var nbLikes = data.datas.nbLikes;
    var nbDisLikes = data.datas.nbDisLikes;
//    var date = Date.parse(data.datas.datePublication.date);
    var date = data.datas.datePublication.date;
    var isfocus = data.datas.isfocus;

    if (isfocus === true) {
        $('#ModalFocus_' + idEvent).css({"display": "none"});
        $('#ModalFocus_' + idEvent).removeClass("in");
        $(".icon_focus_hidden_" + idQuestion).hide();
        $('.blockContenuFocusQuestion_' + idQuestion).removeClass("icon_focus_on");
    } else {
        $('#ModalFocus_' + idEvent).removeClass("hidden");
        $('#ModalFocus_' + idEvent).css({"display": "block", "position": "fixed", "background-color": "rgba(0, 0, 0, 0.7)"});
        $('#ModalFocus_' + idEvent).addClass("in");
        $('.nbLikeValue_focus').text(nbLikes);
        $('.nbDisLikeValue_focus').text(nbDisLikes);
        $('.datePublication_focus').text(date);
        if (auteur === null) {
            $('.author_focus').text("Annonyme");
        } else {
            $('.author_focus').text(auteur);
        }

        $('.contenuQuestionFocus p').text(contenu);
        $(".icon_focus_hidden_" + idQuestion).show();

        if (!$('.iconFavo_' + idQuestion).hasClass("icon_focus_hidden_" + idQuestion)) {
            $('.blockContenuFocusQuestion_' + idQuestion).prepend("<span class='icon_focus icon_focus_on'>");
        }

    }

    $('.close_focus').on('click', function () {
        $('#ModalFocus_' + idEvent).css({"display": "inline", "position": "inherit"});
        $('#ModalFocus_' + idEvent).css({"position": "inherit"});
        $('#ModalFocus_' + idEvent).removeClass("in");
        $('#ModalFocus_' + idEvent).addClass("hidden");
    });

    //append icon favoris sur les question focus

});


//------------delete question --------------------------------------
client.subscribe("/deleteQuestion", function (data) {
    var idQuestion = data.id;
    $('.q_'+idQuestion).remove();
});

//------------change the content of question --------------------------------------
client.subscribe("/updateContentQuestion", function (data) {
    var idQuestion = data.datas.idQuestion;
    var contenu = data.datas.contenu;
    $('.blockContenuFocusQuestion_'+idQuestion).replaceWith("<p>"+contenu+"</p>");
});


//refresh data table admin list of question
client.subscribe("/refreshQuestion", function (data) {
    $('.table-question-admin').bootstrapTable('refresh',
            {url: Routing.generate('_load_question_json', {'id': data.idEvent})}
    );
    
});

