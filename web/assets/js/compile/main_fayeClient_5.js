var client = new Faye.Client('http://localhost:8001/');


//------------Ajouter des questions------------------------
var supscription_add_question = client.subscribe('/addQuestion', function (data) {


    var idEventOld = $('.question:first .blockLike .likeButton .likebutton').data("event");
    var idEvent = data.datas.idEvent;

    var date = Date.parse(data.datas.datePublication.date);
    var auteur = "";
    var class_nbLikeValue = "nbLikeValue_" + data.datas.id;
    var class_nbDisLikeValue = "nbDisLikeValue_" + data.datas.id;

    if (data.datas.auteur === null) {
        auteur = "Anonyme";
    } else {
        auteur = data.datas.auteur;
    }

    if (idEventOld === idEvent) {
        $('.list-question').prepend("<section class='question'>\n\
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
                                <span class='dataPublication'>" + date.toString("dd-MM-yyyy à HH:mm:ss") + "</span>\n\
                                </section>\n\
                                </section>\n\
                                </section>");
        $('.emptyQuestion').remove();
    }

});




//-------------Update likes---------------------------------------
var supscription_update_like = client.subscribe('/updateLikes', function (data) {
    var class_nbLikeValue = "nbLikeValue_" + data.datas.id;
    $('.nbLikeValue_' + data.datas.id).replaceWith("<span class=" + class_nbLikeValue + ">" + data.datas.nbLikes + "</span>");
});

//-------------Update Dislikes---------------------------------------
var supscription_update_dislike = client.subscribe('/updateDisLikes', function (data) {
    var class_nbDisLikeValue = "nbDisLikeValue_" + data.datas.id;
    $('.nbDisLikeValue_' + data.datas.id).replaceWith("<span class=" + class_nbDisLikeValue + ">" + data.datas.nbDisLikes + "</span>");
});


//------------update count question --------------------------------------
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
    $('.count_admin_' + idEvent).replaceWith("<span class = view-event-question-count count_admin_" + idEvent + ">" + result + "</span>");
});
