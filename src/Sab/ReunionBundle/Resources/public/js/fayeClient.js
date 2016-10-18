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

    if (data.datas.auteur === "") {
        auteur = "Anonyme";
    } else {
        auteur = data.datas.auteur;
    }

    if (idEventOld === idEvent) {
         var url = Routing.generate('_user_ajouter_commentaire', {'question_id' : data.datas.id,'parentCommentId' :data.datas.parentCommentId});
         jsDate = new Date(date);
         console.log("date symfony:"+date);
         // console.log("date js:"+jsDate);
         // console.log("formaté--- "+ jsDate.toLocaleDateString() )
    $('#ModalAddQuestion').after("<div class=\"modal fade\"  role=\"dialog\" data-backdrop='true' tabindex='-1'  id='listCommentaire_"+data.datas.id+"'><div class='modal-dialog'><div class='modal-content liste_commentaire'><div class=\"modal-header commentaire_title\"><button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button><h4 style=\"font-weight:bold;color:#01948E\">Commentaire(s):</h4></div><div class='modal-body-commentaire commentaireForQuestion_+"+data.datas.id+"'></div><div class=\"row\"><div class=\"col-md-4 col-md-offset-7 col-xs-12\"><button type=\"button\" class=\"btnAddComment col-xs-12\" data-dismiss=\"modal\"  data-toggle=\"modal\" onclick=\"addCommentInModal("+data.datas.id+",0)\">Ajouter un commentaire</button></div></div></div></div></div>");
       
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
                                <span class='dataPublication'>" + jsDate.toLocaleDateString() + " à " + jsDate.toLocaleTimeString() + "</span>\n\
                                </section>\n\
                                </section>\n\
                                </section>\n\
                                <div class='commentaire_container'>\n\
                                <div class='header_commentaire'><span class='glyphicon glyphicon-comment buttonAddCommentaire'><button type='button' onclick='addCommentInModal("+data.datas.id+",0)' id='btn_comment' class='button_plus' data-question='"+data.datas.id+"' >\n\
                                Commenter    |</button></span><span class='nbre_commentaire'><button type=\"button\" class=\"buttonListCommentaire\" data-toggle=\"modal\" data-target=\"#listCommentaire_"+data.datas.id+"\">(0) Commentaire(s)</button></span></div>\n\
                                <div class='commentaire'>\n\
                                </section>\n\
                                </div>\n\
                                </div>\n\
                                <div class='modal fade form_commentaire' role='dialog' id='form_commentaire_"+data.datas.id+"' style='display : none;'>\n\
                                <div class = 'modal-dialog'>\n\
                                <div class ='modal-content formAddCommentaire'>\n\
                                <div class = 'modal-header modal-header-form'>\n\
                                <button  type='button' class='close close-mobile'  onclick='show_hide_form(this, "+data.datas.id+")'>&times;</button>\n\
                                <h4 class='modal-title'>Commentaire:</h4>\n\
                                </div>\n\
                                <div class='modal-body modal-body-form'></div>\n\
                                </form> \n\
                                </div>\n\
                                </div>\n\
                                </div>\n\
                                </div>\n\
                                ");


        

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
        if (auteur === "") {
            $('.author_focus').text("Anonyme");
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
    $('#comment_'+idQuestion).remove();

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


//delete commentaire
client.subscribe("/deleteCommentaire", function(data){
    var idCommentaire = data.id;
    $('.q_'+idCommentaire).remove();
});

//change the content of commentaire
client.subscribe("/updateContentCommentaire", function (data){
    var idCommentaire = data.datas.idCommentaire;
    var texte = data.datas.texte;
    $('.blockContenuFocusCommentaire_'+idCommentaire).replaceWith("<p>"+texte+"</p>");
})

//refresh data table admin list of commentaire
client.subscribe("/refreshCommentaire", function(data){
    $('.table-commentaire-admin').bootstrapTable('refresh',
        {url: Routing.generate('_load_commentaire_json',{'id': data.idQuestion})}
    );
})  



var supscription_add_commentaire = client.subscribe('/addCommentaire', function (data) {

    var idQuestion = data.datas.idQuestion;
    var date = data.datas.datePublication.date;
    jsDate = new Date(date);
    var auteur = "";
    var parentCommentId = data.datas.parentCommentId;
    var level = data.datas.level;
    if (data.datas.auteur === "") {
        auteur = "Anonyme";
    } else {
        auteur = data.datas.auteur;
    }
    
    console.log(idQuestion);
    if (level == 1 ){
        $('.blockContenuCommentaire_'+idQuestion).empty();
        $('.blockContenuCommentaire_'+idQuestion).prepend("<div class='contenuCommentaire blockContenuFocusCommentaire_"+data.datas.id+"'>\n\
                                <p>" + data.datas.texte + "</p>\n\
                                <section class='c_auteur'>\n\
                                posté par : <span>" + auteur + " - </span>\n\
                                <span class='dataPublication'>" + jsDate.toLocaleDateString() + "</span>\n\
                                </section>\n\
                                </div>");
        $('.blockContenuCommentaire_'+idQuestion).show();
        $('.commentaireForQuestion_'+idQuestion).prepend("<div class='modal_contenuCommentaire blockContenuFocusCommentaire_"+data.datas.id+"'>\n\
                                <p>"+data.datas.texte+"</p>\n\
                                <div ><p class='commentCommentaire'>\n\
                                <span class='commentComment' data-dismiss='modal' onclick='addCommentInModal("+idQuestion+","+parentCommentId+")''>Répondre à ce commentaire</span>\n\
                                                </p>\n\
                                <p class='c_auteur'>\n\
                                <span>"+auteur+"</span>\n\
                                <span class='c_datePublication'> - "+jsDate.toLocaleDateString()+"</span>\n\
                                </p>\n\
                                </p></div>");
    }else {
    
        $('.blockContenuCommentaire_'+idQuestion).show();
        $('.blockContenuFocusCommentaire_'+parentCommentId+' div.commentairelevel2' ).append('<div class="commentLevel2 row" style="margin:0">\n\
                                                        <p style="word-wrap:break-word;">'+data.datas.texte+'</p>\n\
                                                        <section class="c_auteurLevel2">\n\
                                                            <span  style="word-wrap:break-word;">'+auteur+'</span>\n\
                                                            <span class="c_datePublication"> - '+jsDate.toLocaleDateString()+'</span>\n\
                                                        </section>\n\
                                                    </div>');
    }

});


client.subscribe("/updateCountCommentaire", function(data){
    var idQuestion = data.idQuestion;
    var nbre_commentaire = data.nbre_commentaire;
    $('.nbre_commentaire_'+ idQuestion).replaceWith("("+nbre_commentaire+")<button type='button' class='buttonListCommentaire' data-toggle='modal' data-target='#listCommentaire_"+idQuestion+"'>Commentaire(s)</button>");
});



//tunnel addCommentaire (anass)
client.subscribe("/addCommentaire", function(data){
    var idQuestion = data.idQuestion;
    var nbre_commentaire = data.nbre_commentaire;
    $('.nbre_commentaire_'+ idQuestion).replaceWith("("+nbre_commentaire+")<button type='button' class='buttonListCommentaire' data-toggle='modal' data-target='#listCommentaire_"+idQuestion+"'>Commentaire(s)</button>");
});

