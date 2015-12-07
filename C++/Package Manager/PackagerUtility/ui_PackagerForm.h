/********************************************************************************
** Form generated from reading UI file 'PackagerForm.ui'
**
** Created: Sat Dec 14 21:07:24 2013
**      by: Qt User Interface Compiler version 4.8.1
**
** WARNING! All changes made in this file will be lost when recompiling UI file!
********************************************************************************/

#ifndef UI_PACKAGERFORM_H
#define UI_PACKAGERFORM_H

#include <QtCore/QVariant>
#include <QtGui/QAction>
#include <QtGui/QApplication>
#include <QtGui/QButtonGroup>
#include <QtGui/QDialog>
#include <QtGui/QFrame>
#include <QtGui/QHeaderView>
#include <QtGui/QPushButton>
#include <QtGui/QTableView>
#include <QtGui/QVBoxLayout>
#include <QtGui/QWidget>

QT_BEGIN_NAMESPACE

class Ui_PackagerForm
{
public:
    QWidget *verticalLayoutWidget;
    QVBoxLayout *verticalLayout;
    QPushButton *newBttn;
    QPushButton *loadBttn;
    QPushButton *saveBttn;
    QFrame *line;
    QPushButton *addBttn;
    QPushButton *expBttn;
    QPushButton *remBttn;
    QFrame *line_2;
    QPushButton *chngBttn;
    QPushButton *prevBttn;
    QFrame *line_3;
    QPushButton *closeBttn;
    QTableView *tableView;

    void setupUi(QDialog *PackagerForm)
    {
        if (PackagerForm->objectName().isEmpty())
            PackagerForm->setObjectName(QString::fromUtf8("PackagerForm"));
        PackagerForm->resize(656, 285);
        QSizePolicy sizePolicy(QSizePolicy::Fixed, QSizePolicy::Fixed);
        sizePolicy.setHorizontalStretch(0);
        sizePolicy.setVerticalStretch(0);
        sizePolicy.setHeightForWidth(PackagerForm->sizePolicy().hasHeightForWidth());
        PackagerForm->setSizePolicy(sizePolicy);
        verticalLayoutWidget = new QWidget(PackagerForm);
        verticalLayoutWidget->setObjectName(QString::fromUtf8("verticalLayoutWidget"));
        verticalLayoutWidget->setGeometry(QRect(520, 10, 131, 271));
        verticalLayout = new QVBoxLayout(verticalLayoutWidget);
        verticalLayout->setObjectName(QString::fromUtf8("verticalLayout"));
        verticalLayout->setContentsMargins(0, 0, 0, 0);
        newBttn = new QPushButton(verticalLayoutWidget);
        newBttn->setObjectName(QString::fromUtf8("newBttn"));

        verticalLayout->addWidget(newBttn);

        loadBttn = new QPushButton(verticalLayoutWidget);
        loadBttn->setObjectName(QString::fromUtf8("loadBttn"));

        verticalLayout->addWidget(loadBttn);

        saveBttn = new QPushButton(verticalLayoutWidget);
        saveBttn->setObjectName(QString::fromUtf8("saveBttn"));

        verticalLayout->addWidget(saveBttn);

        line = new QFrame(verticalLayoutWidget);
        line->setObjectName(QString::fromUtf8("line"));
        line->setFrameShape(QFrame::HLine);
        line->setFrameShadow(QFrame::Sunken);

        verticalLayout->addWidget(line);

        addBttn = new QPushButton(verticalLayoutWidget);
        addBttn->setObjectName(QString::fromUtf8("addBttn"));

        verticalLayout->addWidget(addBttn);

        expBttn = new QPushButton(verticalLayoutWidget);
        expBttn->setObjectName(QString::fromUtf8("expBttn"));

        verticalLayout->addWidget(expBttn);

        remBttn = new QPushButton(verticalLayoutWidget);
        remBttn->setObjectName(QString::fromUtf8("remBttn"));

        verticalLayout->addWidget(remBttn);

        line_2 = new QFrame(verticalLayoutWidget);
        line_2->setObjectName(QString::fromUtf8("line_2"));
        line_2->setFrameShape(QFrame::HLine);
        line_2->setFrameShadow(QFrame::Sunken);

        verticalLayout->addWidget(line_2);

        chngBttn = new QPushButton(verticalLayoutWidget);
        chngBttn->setObjectName(QString::fromUtf8("chngBttn"));

        verticalLayout->addWidget(chngBttn);

        prevBttn = new QPushButton(verticalLayoutWidget);
        prevBttn->setObjectName(QString::fromUtf8("prevBttn"));

        verticalLayout->addWidget(prevBttn);

        line_3 = new QFrame(verticalLayoutWidget);
        line_3->setObjectName(QString::fromUtf8("line_3"));
        line_3->setFrameShape(QFrame::HLine);
        line_3->setFrameShadow(QFrame::Sunken);

        verticalLayout->addWidget(line_3);

        closeBttn = new QPushButton(verticalLayoutWidget);
        closeBttn->setObjectName(QString::fromUtf8("closeBttn"));

        verticalLayout->addWidget(closeBttn);

        tableView = new QTableView(PackagerForm);
        tableView->setObjectName(QString::fromUtf8("tableView"));
        tableView->setGeometry(QRect(10, 10, 501, 271));

        retranslateUi(PackagerForm);

        QMetaObject::connectSlotsByName(PackagerForm);
    } // setupUi

    void retranslateUi(QDialog *PackagerForm)
    {
        PackagerForm->setWindowTitle(QApplication::translate("PackagerForm", "PackagerForm", 0, QApplication::UnicodeUTF8));
        newBttn->setText(QApplication::translate("PackagerForm", "New package", 0, QApplication::UnicodeUTF8));
        loadBttn->setText(QApplication::translate("PackagerForm", "Load package...", 0, QApplication::UnicodeUTF8));
        saveBttn->setText(QApplication::translate("PackagerForm", "Save package...", 0, QApplication::UnicodeUTF8));
        addBttn->setText(QApplication::translate("PackagerForm", "Add Files...", 0, QApplication::UnicodeUTF8));
        expBttn->setText(QApplication::translate("PackagerForm", "Export Files...", 0, QApplication::UnicodeUTF8));
        remBttn->setText(QApplication::translate("PackagerForm", "Remove Files", 0, QApplication::UnicodeUTF8));
        chngBttn->setText(QApplication::translate("PackagerForm", "Change Key...", 0, QApplication::UnicodeUTF8));
        prevBttn->setText(QApplication::translate("PackagerForm", "Preview...", 0, QApplication::UnicodeUTF8));
        closeBttn->setText(QApplication::translate("PackagerForm", "Close", 0, QApplication::UnicodeUTF8));
    } // retranslateUi

};

namespace Ui {
    class PackagerForm: public Ui_PackagerForm {};
} // namespace Ui

QT_END_NAMESPACE

#endif // UI_PACKAGERFORM_H
