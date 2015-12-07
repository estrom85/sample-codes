#ifndef RECEIPTDESCRIPTION_H
#define RECEIPTDESCRIPTION_H

#include <QString>
#include <QImage>
#include <QPainter>
#include <QPrinter>
#include "dbconnection.h"

class RecipeDescription
{
public:
    RecipeDescription(DBConnection &db, int mId);
    RecipeDescription();
    ~RecipeDescription();

    void setId(int mId);
    int getId();

    void setName(QString &name);
    QString getName();

    void setIngredients(QString &ingredients);
    QString getIngredients();

    void setRecipe(QString &recipe);
    QString getRecipe();

    void setImage(QString &path);
    void setImage(QImage *image);
    const QImage *getImage();

    bool saveRecipe(DBConnection &db);
    bool deleteRecipe();

    void printRecipe(QPrinter &printer);

private:
    void setDefault();
    void setDefaultImage();
    QImage *createImage(QImage* img=0);

    QString getImagePath();

    void drawBorderH(QPainter &painter, int x, int y,
                    int x2, int y2,
                    QColor &color1, QColor &color2);
    void drawBorderV(QPainter &painter, int x, int y,
                    int x2, int y2,
                    QColor &color1, QColor &color2);

    int drawText(QPainter &painter, int x, int y, int width, int flags, QString text, QFont font=QFont());

    QImage *clipImage(QImage* img);

private:
    int mId;
    QString name;
    QString ingredients;
    QString recipe;

    QImage *image;
    //QString imagePath;

    const static int WIDTH=200;
    const static int HEIGHT=200;
    const static QString PATH;
    const static QString EXT;
};

#endif // RECEIPTDESCRIPTION_H
