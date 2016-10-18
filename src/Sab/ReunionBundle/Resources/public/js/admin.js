(function (window) {

    'use strict';

    function classReg(className) {
        return new RegExp("(^|\\s+)" + className + "(\\s+|$)");
    }

    // classList support for class management
    // altho to be fair, the api sucks because it won't accept multiple classes at once
    var hasClass, addClass, removeClass;

    if ('classList' in document.documentElement) {
        hasClass = function (elem, c) {
            return elem.classList.contains(c);
        };
        addClass = function (elem, c) {
            elem.classList.add(c);
        };
        removeClass = function (elem, c) {
            elem.classList.remove(c);
        };
    }
    else {
        hasClass = function (elem, c) {
            return classReg(c).test(elem.className);
        };
        addClass = function (elem, c) {
            if (!hasClass(elem, c)) {
                elem.className = elem.className + ' ' + c;
            }
        };
        removeClass = function (elem, c) {
            elem.className = elem.className.replace(classReg(c), ' ');
        };
    }

    function toggleClass(elem, c) {
        var fn = hasClass(elem, c) ? removeClass : addClass;
        fn(elem, c);
    }

    var classie = {
        // full names
        hasClass: hasClass,
        addClass: addClass,
        removeClass: removeClass,
        toggleClass: toggleClass,
        // short names
        has: hasClass,
        add: addClass,
        remove: removeClass,
        toggle: toggleClass
    };

    // transport
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(classie);
    } else {
        // browser global
        window.classie = classie;
    }

})(window);

//  cloturer et/ou ouvrire un événement
function switchEvent(val_this) {
    var statut_event = val_this.getAttribute('data-statut');
    var id_event = val_this.getAttribute('data-event');
    if (statut_event === "on") {
        switchEventOn(id_event);
    }
    if (statut_event === "off") {
        switchEventOff(id_event);
    }
}

// ouvrir un événement
var switchEventOn = function (id_event) {
    $.ajax({
        type: "POST",
        url: Routing.generate('_ouvire_event', {'id': id_event}),
        success: function (result) {
            // changer le data statut a off
            $('.switch_label_' + id_event).attr("data-statut", "off");

            //remove class event_enabled
            $('.event_' + id_event).removeClass("event_enabled");
            //add class event_disabled
            $('.event_' + id_event).addClass("event_disabled");

        },
        error: function () {
            console.log("");
        },
        complete: function () {
            console.log("");
        }
    });
}

// Fermer un événement
var switchEventOff = function (id_event) {
    $.ajax({
        type: "POST",
        url: Routing.generate('_cloturer_event', {'id': id_event}),
        success: function (result) {
            // changer le data statut a on
            $('.switch_label_' + id_event).attr("data-statut", "on");

            //remove class event_disabled
            $('.event_' + id_event).removeClass("event_disabled");
            //addclass event_enabled
            $('.event_' + id_event).addClass("event_enabled");

        },
        error: function () {
            console.log("");
        },
        complete: function () {
            console.log("");
        }
    });
}

// Supprimer un événement
function deleteEvent(val_this) {
    var reponse = confirm("voulez-vous supprimer cet événement ?");
    if (reponse) {
        var id_event = val_this.getAttribute('data-event');
        $.ajax({
            type: "POST",
            url: Routing.generate('_delete_event', {'id': id_event}),
            success: function (result) {
                $('.event_' + id_event).hide();
            },
            error: function () {
                console.log("error_delete");
            },
            complete: function () {
                console.log("complete_delete");
            }
        });
    }
}


$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $(".form-control-date").datetimepicker({
        language: 'fr',
    });
    //focusQuestion();
})


// Vérifier si le login existe déja dans la base de données  
function checkErorsEvent(val_this) {

    var name = val_this.getAttribute('data-name');
    var valeur = $("#" + val_this.getAttribute('id')).val();

    // method ajax called method controller 
    $.ajax({
        type: "POST",
        data: {'name': name, 'value': valeur},
        dataType: 'json',
        url: Routing.generate('_check_errors_event'),
        success: function (resultat) {
            var valAction = resultat.val;
            var nameAction = resultat.nameAction;
            if (valAction === "yes") {
                if (nameAction === "labelEvent") {
                    $(".form-group-label-event").append("<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert'>&times;</a><div>" + valeur + " existe déja veuillez saisir un autre nom de l'événement !</div></div>");
                    $('.add-edit-event-Btn').attr("disabled", "disabled");
                    return;
                }

                if (nameAction === "username") {
                    $(".form-group-username").append("<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert'>&times;</a><div>" + valeur + " existe déja veuillez saisir un autre identifiant !</div></div>");
                    $('.add-edit-event-Btn').attr("disabled", "disabled");
                    return;
                }

                if (nameAction === "password") {
                    $(".form-group-password").append("<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert'>&times;</a><div>" + valeur + " existe déja veuillez saisir un autre mot de passe !</div></div>");
                    $('.add-edit-event-Btn').attr("disabled", "disabled");
                    return;
                }

            }
            if (valAction === "no") {
                $('.add-edit-event-Btn').removeAttr("disabled");
            }

        },
        error: function () {

        },
        complete: function () {

        }
    });
    return false;
}

//Filtrer les événemets (ouvert et fermer)
function filtrer() {
    var valeur_selected = $('#filtre_event').val();
    if (valeur_selected === "ouvert") {
        $('.list-view-event li').filter('.event_disabled').hide();
        $('.list-view-event li').filter('.event_enabled').show();
    }
    if (valeur_selected === "fermer") {
        $('.list-view-event li').filter('.event_enabled').hide();
        $('.list-view-event li').filter('.event_disabled').show();
    }
    if (valeur_selected === "tout") {
        $('.list-view-event li').filter('.event_disabled').show();
        $('.list-view-event li').filter('.event_enabled').show();
    }

}

//Mettre une coleur pour la ligne selectionnée (BACKEND - liste des questions )
function rowStyleFocus(row, index) {
    var classes = {
        true: 'focus-enabled',
        false: 'focus-disbled'
    }
    return {
        classes: classes[row.statutFocus]
    }
}


//Répondre à une question (popUp)
function focusQuestion() {

    $('.table-question-admin').on("click-row.bs.table", function (row, $element) {
        var idEvent = $element.idEvent;
        var idQuestion = $element.idQuestion;
        var focusStatut = $element.statutFocus;
        $.ajax({
            type: "POST",
            data: {'idEvent': idEvent, 'idQuestion': idQuestion, 'clickStatut': focusStatut},
            dataType: 'json',
            url: Routing.generate('_focus_question'),
            success: function (res) {
                console.log("");
            },
            error: function () {
            },
            complete: function () {
                $('.table-question-admin').bootstrapTable('refresh',
                        {url: Routing.generate('_load_question_json', {'id': idEvent})}
                );
            }
        });
    });
}

function deleteQuestion(id, idEvent) {
    var confirmDelete = confirm("Voulez-vous vraiment supprimer cette question ?");
    if (confirmDelete === true) {
        $.ajax({
            type: 'POST',
            url: Routing.generate('_delete_question', {'id': id}),
            success: function (res) {
                console.log(res);
            },
            error: function () {
                console.log("error");
            },
            complete: function () {
                $('.table-question-admin').bootstrapTable('refresh',
                        {url: Routing.generate('_load_question_json', {'id': idEvent})}
                );
            }
        });
    }
}

function modifierQuestion(question, idEvent) {

    $.ajax({
        type: 'POST',
        url: Routing.generate('_modification_question', {'id': question}),
        success: function (res) {
            bootbox.dialog({
                title: "Modifier la question",
                message: "<div class='row'>" +
                        "<div class='col-md-12'>" +
                        "<textarea id='contenuQuestionModifier' rows='4' cols='80'>" +
                        res +
                        "</textarea>" +
                        "</div>" +
                        "</div>",
                buttons: {
                    success: {
                        label: "Modifier",
                        className: "btn-success",
                        callback: function () {
                            if ($('#contenuQuestionModifier').val() == "") {
                                bootbox.alert("Le champs texte ne doit pas être vide");
                            } else {
                                //ajax
                                $.ajax({
                                    type: 'POST',
                                    data: {'idQuestion': question, 'contenu': $('#contenuQuestionModifier').val()},
                                    dataType: 'json',
                                    url: Routing.generate('_save_modification_question'),
                                    success: function (res) {
                                        bootbox.alert("La modification de votre question a bien été prise en compte");
                                    },
                                    complete: function () {
                                        $('.table-question-admin').bootstrapTable('refresh',
                                                {url: Routing.generate('_load_question_json', {'id': idEvent})}
                                        );
                                    }
                                });
                            }
                        }
                    },
                }
            });

        },
        error: function () {
            console.log("error");
        },
        complete: function () {
            $('.table-question-admin').bootstrapTable('refresh',
                    {url: Routing.generate('_load_question_json', {'id': idEvent})}
            );
        }
    });
}

// Fonction relative aux commentaires

function listerCommentaire (question) 
{   
    url=Routing.generate ('_list_commentaire' ,{'id':question});
    window.document.location.href=url;
    
}

function listerReponses (commentaire) 
{   
    url=Routing.generate ('_list_reponses' ,{ 'id' : commentaire});
    window.document.location.href=url;
    
}

function deleteCommentaire (id, idQuestion){
    var confirmDelete = confirm("Voulez-vous vraiment supprimer ce commentaire ?");
    if (confirmDelete === true) {
        $.ajax({
            type: 'POST',
            url: Routing.generate('_delete_commentaire', {'id': id}),
            success: function (res) {
                console.log(res);
            },
            error: function () {
                console.log("error");
            },
            complete: function () {
                $('.table-commentaire-admin').bootstrapTable('refresh',
                        {url: Routing.generate('_load_commentaire_json', {'id': idQuestion})}
                );
            }
        });
    }
}

function modifierCommentaire(commentaire, idQuestion) {

    $.ajax({
        type: 'POST',
        url: Routing.generate('_modification_commentaire', {'id': commentaire}),
        success: function (res) {
            bootbox.dialog({
                title: "Modifier le commentaire:",
                message: "<div class='row'>" +
                        "<div class='col-md-12'>" +
                        "<textarea id='contenuCommentaireModifier' rows='4' cols='80'>" +
                        res +
                        "</textarea>" +
                        "</div>" +
                        "</div>",
                buttons: {
                    success: {
                        label: "Modifier",
                        className: "btn-success",
                        callback: function () {
                            if ($('#contenuCommentaireModifier').val() == "") {
                                bootbox.alert("Le champs texte ne doit pas être vide");
                            } else {
                                //ajax
                                $.ajax({
                                    type: 'POST',
                                    data: {'idCommentaire': commentaire, 'texte': $('#contenuCommentaireModifier').val()},
                                    dataType: 'json',
                                    url: Routing.generate('_save_modification_commentaire'),
                                    success: function (res) {
                                        bootbox.alert("La modification de votre commentaire a bien été prise en compte");
                                    },
                                    complete: function () {
                                        $('.table-commentaire-admin').bootstrapTable('refresh',
                                                {url: Routing.generate('_load_commentaire_json', {'id': idQuestion})}
                                        );
                                    }
                                });
                            }
                        }
                    },
                }
            });

        },
        error: function () {
            console.log("error");
        },
        complete: function () {
            $('.table-commentaire-admin').bootstrapTable('refresh',
                    {url: Routing.generate('_load_commentaire_json', {'id': idQuestion})}
            );
        }
    });
}



