#include "receiptdescription.h"

const QString RecipeDescription::PATH("pics/recipe");
const QString RecipeDescription::EXT(".png");

RecipeDescription::RecipeDescription(DBConnection &db, int id){
    //setDefault();
    this->mId=id;
    QString sql="";
    QTextStream ss(&sql);

    ss<<"SELECT * FROM items WHERE id="<<id;
    QueryResult result=db.executeSQL(sql);
    if(!result.next())
        return;

    name=result.getValueString("name");

    sql.clear();

    ss<<"SELECT * FROM Receipts WHERE id="<<id;

    result=db.executeSQL(sql);

    if(!result.next())
        return;

    ingredients=result.getValueString("ingredients");
    recipe=result.getValueString("receipt");
    //imagePath=result.getValueString("image");
    //qDebug()<<"Receipt created";
    setDefaultImage();
}

RecipeDescription::RecipeDescription(){
    mId=0;
    setDefaultImage();
}

RecipeDescription::~RecipeDescription(){
    delete image;
}

void RecipeDescription::setId(int id)
{
    if(this->mId==0)
        this->mId=id;
}

int RecipeDescription::getId()
{
    return mId;
}

void RecipeDescription::setName(QString &name)
{
    this->name=name;
}

QString RecipeDescription::getName()
{
    return name;
}

void RecipeDescription::setIngredients(QString &ingredients)
{
    if(!ingredients.isEmpty())
        this->ingredients=ingredients;
}

QString RecipeDescription::getIngredients()
{
    return ingredients;
}

void RecipeDescription::setRecipe(QString &recipe)
{
    if(!recipe.isEmpty())
        this->recipe=recipe;
}

QString RecipeDescription::getRecipe()
{
    return recipe;
}

void RecipeDescription::setImage(QString &path)
{
    QImage *img=new QImage(path);

    if(img->isNull())
        delete img;
    else{
        delete image;
        image=createImage(img);
        delete img;
    }
}

void RecipeDescription::setImage(QImage *image)
{
    if(image==0)
        return;
    delete this->image;
    this->image=createImage(image);
}

const QImage *RecipeDescription::getImage()
{
    return image;
}

bool RecipeDescription::saveRecipe(DBConnection &db)
{
    if(mId==0)
        return false;

    QString text;
    QString image("");
    QTextStream ss(&text);
    ss<<"SELECT * FROM Receipts WHERE id="<<mId;
    //qDebug()<<text;
    QueryResult result=db.executeSQL(text);
    if(result.next())
        db.editReceipt(mId,ingredients,recipe,image);
    else
        db.addReceipt(mId,ingredients,recipe,image);

    text.clear();
    this->image->save(getImagePath());

    return true;

}

bool RecipeDescription::deleteRecipe()
{
    QFile(getImagePath()).remove();
}

void RecipeDescription::printRecipe(QPrinter &printer)
{
    double xRate=1/10.0;
    double yRate=1/15.0;
    QRect paper=printer.paperRect();
    QRect page((int)(paper.width()*xRate),(int)(paper.height()*yRate),
               (int)(paper.width()*(1-2*xRate)),(int)(paper.height()*(1-2*yRate)));
    int linePointer=page.y();

    QFont font;
    QPainter paint;

    paint.begin(&printer);

    font.setFamily("Comic Sans MS");
    font.setBold(true);
    font.setItalic(true);
    font.setPointSize(30);
    qDebug()<<linePointer;
    linePointer=drawText(paint,page.x(),linePointer,page.width(),Qt::TextWordWrap|Qt::AlignCenter,this->name,font);

    paint.drawImage(page.width()+page.x()-image->width(),linePointer,*this->image);

    int imgBottom=linePointer+image->height()+40;
    font.setPointSize(16);
    font.setItalic(false);

    linePointer=drawText(paint,page.x(),linePointer,page.width()-image->width()-40,Qt::TextWordWrap,QString::fromUtf8("Ingrediencie:"),font);

    linePointer+=20;
    font.setPointSize(12);
    font.setBold(false);

    linePointer=drawText(paint,page.x(),linePointer,page.width()-image->width()-40,Qt::TextWordWrap|Qt::AlignJustify,this->ingredients,font);

    if(linePointer<imgBottom)
        linePointer=imgBottom;

    font.setPointSize(16);
    font.setBold(true);

    linePointer=drawText(paint,page.x(),linePointer,page.width(),Qt::TextWordWrap,QString::fromUtf8("Postup:"),font);

    font.setPointSize(12);
    font.setBold(false);

    linePointer+=20;

    linePointer=drawText(paint,page.x(),linePointer,page.width(),Qt::TextWordWrap|Qt::AlignJustify,this->recipe,font);


    /*
    paint.setFont(font);

   // metrics = new QFontMetrics(font,&printer);

    QTextOption options;
    options.setAlignment(Qt::AlignCenter);
    options.setWrapMode(QTextOption::WordWrap);

    QRect textRect=metrics->boundingRect(page.x(),linePointer,
                                             page.width(),250,Qt::TextWordWrap,this->name);

    paint.drawText(page.x(),linePointer,page.width(),textRect.height(),Qt::TextWordWrap|Qt::AlignCenter,this->name);

    linePointer+=textRect.height()+20;
*/

    //paint.drawImage(page.width()-image->width(),linePointer,*this->image);

    /*

    delete metrics;
    metrics=new QFontMetrics(font,&printer);
    textRect=metrics->boundingRect(page.x(),linePointer,
                                   page.width()-image->width()-20,
                                   250,Qt::TextWordWrap,
                                   QString::fromUtf8("Ingrediencie:"));

    paint.setFont(font);
    paint.drawText(page.x(),linePointer,page.width()-image->width()-20,
                   textRect.height(),Qt::TextWordWrap,QString::fromUtf8("Ingrediencie:"));

    linePointer+=image->height()+20;

*/
    paint.end();
}

void RecipeDescription::setDefaultImage(){
    QString path=getImagePath();

    QFile imgFile(path);
    if(!imgFile.exists())
        image=createImage();
    else
        image=new QImage(path);
    //qDebug()<<"image created";
}

QImage *RecipeDescription::createImage(QImage *img)
{
/*
    QImage *output=new QImage(WIDTH,HEIGHT,QImage::Format_ARGB32);
    output->fill(0xFFFFFFFF);
    QPainter painter(output);

    QRect rect(0,0,WIDTH,HEIGHT);
    if(img)
        painter.drawImage(rect,*img);
*/

    QImage *output=clipImage(img);
    QPainter painter(output);

    static const int BORDER=6;

    QColor light(188,188,188);
    QColor dark(95,95,95);

    drawBorderH(painter,0,0,WIDTH,BORDER,light,dark);
    drawBorderV(painter,0,0,BORDER,HEIGHT,light,dark);
    drawBorderH(painter,WIDTH,HEIGHT,0,HEIGHT-BORDER,dark,light);
    drawBorderV(painter,WIDTH,HEIGHT,WIDTH-BORDER,0,dark,light);

    return output;
}

QString RecipeDescription::getImagePath()
{
    QString output;
    QTextStream ss(&output);
    ss<<PATH<<mId<<EXT;

    return output;
}

void RecipeDescription::drawBorderH(QPainter &painter, int x1, int y1,
                                   int x2, int y2,
                                   QColor &color1, QColor &color2)
{
    QPoint* points=new QPoint[4];
    int width=y2-y1;

    if(width==0)
        return;
/*
    if(width<0)
        width*=-1;
*/
    if(width%2!=0)
        width++;

    points[0]=QPoint(x1,y1);
    points[1]=QPoint(x2,y1);
    points[2]=QPoint(x2-width/2,y2-width/2);
    points[3]=QPoint(x1+width/2,y1+width/2);
    painter.setBrush(color1);
    painter.setPen(QColor::fromRgb(0,0,0,0));
    painter.drawPolygon(points,4);


    painter.setBrush(color2);
    painter.setPen(QColor::fromRgb(0,0,0,0));
    points[0]=QPoint(x1+width/2,y2-width/2);
    points[1]=QPoint(x2-width/2,y2-width/2);
    points[2]=QPoint(x2-width,y2);
    points[3]=QPoint(x1+width,y2);

    painter.drawPolygon(points,4);

    delete[] points;
}

void RecipeDescription::drawBorderV(QPainter &painter, int x1, int y1, int x2, int y2, QColor &color1, QColor &color2)
{
    QPoint* points=new QPoint[4];
    int width=x2-x1;

    if(width==0)
        return;
/*
    if(width<0)
        width*=-1;
*/
    if(width%2!=0)
        width++;

    points[0]=QPoint(x1,y1);
    points[1]=QPoint(x1,y2);
    points[2]=QPoint(x1+width/2,y2-width/2);
    points[3]=QPoint(x1+width/2,y1+width/2);
    painter.setBrush(color1);
     painter.setPen(QColor::fromRgb(0,0,0,0));
    painter.drawPolygon(points,4);

    painter.setBrush(color2);
     painter.setPen(QColor::fromRgb(0,0,0,0));
    points[0]=QPoint(x1+width/2,y1+width/2);
    points[1]=QPoint(x2-width/2,y2-width/2);
    points[2]=QPoint(x2,y2-width);
    points[3]=QPoint(x2,y1+width);
    painter.drawPolygon(points,4);

    delete[] points;
}

int RecipeDescription::drawText(QPainter &painter, int x, int y, int width, int flags, QString text, QFont font)
{
    QFontMetrics metrics(font,painter.device());
    QRect rect=metrics.boundingRect(x,y,width,500,flags&Qt::TextWordWrap,text);
    qDebug()<<"y: "<<y<<", text y: "<<rect.y();
    painter.setFont(font);
    painter.drawText(x,y,width,rect.height(),flags,text);

    return rect.y()+rect.height()+20;
}

QImage *RecipeDescription::clipImage(QImage *img)
{

    QImage *output=new QImage(WIDTH,HEIGHT,QImage::Format_ARGB32);
    output->fill(0xFFFFFFFF);
    if(img==0) return output;
    QPainter painter(output);
    QRect src=img->rect();
    int x=0,y=0,width=WIDTH,height=HEIGHT;

    double HRatio=(double)src.height()/HEIGHT;
    double VRatio=(double)src.width()/WIDTH;

    if(VRatio>HRatio){
        width=src.width()/HRatio;
        x=-(width-WIDTH)/2;
    }
    if(HRatio>VRatio){
        height=src.height()/VRatio;
        y=-(height-HEIGHT)/2;
    }

    QRect dest(x,y,width,height);

    painter.drawImage(dest,*img);

    return output;
}


